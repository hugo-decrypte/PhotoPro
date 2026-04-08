import pg from 'pg'

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    const config = useRuntimeConfig()
    const query = getQuery(event)
    const code = query.code

    console.log(`[API] Entry - Gallery: ${id}, Code: ${code}`)

    const galleryClient = new pg.Client({
        host: config.dbHost,
        port: Number(config.dbPort) || 5432,
        user: config.dbUser,
        password: config.dbPassword,
        database: config.dbName,
    })

    const photoClient = new pg.Client({
        host: config.photoDbHost,
        port: Number(config.photoDbPort) || 5432,
        user: config.photoDbUser,
        password: config.photoDbPassword,
        database: config.photoDbName,
    })

    try {
        await galleryClient.connect()
        console.log(`[API] Connected to Gallery DB`)

        const gRes = await galleryClient.query(
            'SELECT id, title, description, type, status FROM gallery WHERE id = $1 AND status = $2',
            [id, 'PUBLISHED']
        )

        if (gRes.rows.length === 0) {
            throw createError({ statusCode: 404, message: 'Galerie introuvable' })
        }

        const gallery = gRes.rows[0]

        if (gallery.type === 'PRIVATE') {
            if (!code) {
                return { gallery, photos: null, private: true }
            }

            const check = await galleryClient.query(
                'SELECT 1 FROM private_gallery_access WHERE gallery_id = $1 AND access_code = $2',
                [id, code]
            )

            if (check.rows.length === 0) {
                throw createError({ statusCode: 403, message: 'Code incorrect' })
            }
        }

        const gpRes = await galleryClient.query(
            'SELECT photo_id, "order" FROM gallery_photo WHERE gallery_id = $1 ORDER BY "order"',
            [id]
        )

        let photos = []
        if (gpRes.rows.length > 0) {
            const ids = gpRes.rows.map(r => r.photo_id)

            await photoClient.connect()
            console.log(`[API] Connected to Photo DB`)

            const pRes = await photoClient.query(
                'SELECT id, s3_key, title FROM photo WHERE id = ANY($1)',
                [ids]
            )

            const pMap = new Map(pRes.rows.map(p => [p.id, p]))
            photos = gpRes.rows.map(gp => {
                const p = pMap.get(gp.photo_id)
                return p ? { ...p, order: gp.order, src: `${config.public.s3Endpoint}/photopro-photos/${p.s3_key}` } : null
            }).filter(Boolean)
        }

        return { gallery, photos, private: false }

    } catch (e: any) {
        console.error('[API FATAL ERROR]', e.message)
        throw createError({
            statusCode: e.statusCode || 500,
            message: e.message || 'Erreur serveur'
        })
    } finally {
        await galleryClient.end().catch(() => { })
        await photoClient.end().catch(() => { })
    }
})
