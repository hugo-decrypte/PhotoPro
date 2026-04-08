import pg from 'pg'

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    const config = useRuntimeConfig()

    const galleryClient = new pg.Client({
        host: config.dbHost,
        port: Number(config.dbPort),
        user: config.dbUser,
        password: config.dbPassword,
        database: config.dbName,
    })

    const photoClient = new pg.Client({
        host: config.photoDbHost,
        port: Number(config.photoDbPort),
        user: config.photoDbUser,
        password: config.photoDbPassword,
        database: config.photoDbName,
    })

    try {
        await galleryClient.connect()

        // 🔹 GALERIE
        const galleryResult = await galleryClient.query(`
      SELECT id, title, description, type, status, cover_photo_id, published_at
      FROM gallery
      WHERE id = $1 AND status = 'PUBLISHED'
    `, [id])

        if (galleryResult.rows.length === 0) {
            throw createError({ statusCode: 404, message: 'Galerie introuvable' })
        }

        const gallery = galleryResult.rows[0]

        if (gallery.type === 'PRIVATE') {
            return { gallery, photos: null, private: true }
        }

        // 🔹 PHOTOS (liaison)
        const gpResult = await galleryClient.query(`
      SELECT photo_id, "order"
      FROM gallery_photo
      WHERE gallery_id = $1
      ORDER BY "order" ASC
    `, [id])

        if (gpResult.rows.length === 0) {
            return { gallery, photos: [], private: false }
        }

        const photoIds = gpResult.rows.map(r => r.photo_id)

        // 🔹 DB PHOTO
        await photoClient.connect()

        const photosResult = await photoClient.query(`
      SELECT id, title, s3_key, mime_type
      FROM photo
      WHERE id = ANY($1)
    `, [photoIds])

        const photoMap = new Map(photosResult.rows.map(p => [p.id, p]))

        // 🔥 SAFE MERGE
        const photos = gpResult.rows
            .map(gp => {
                const photo = photoMap.get(gp.photo_id)

                if (!photo) return null // évite crash

                return {
                    ...photo,
                    order: gp.order,
                    url: `${config.public.s3Endpoint}/photopro-photos/${photo.s3_key}`
                }
            })
            .filter(Boolean)

        return { gallery, photos, private: false }

    } catch (err) {
        console.error('API ERROR:', err)

        throw createError({
            statusCode: 500,
            message: 'Erreur serveur galerie'
        })
    } finally {
        await galleryClient.end().catch(() => {})
        await photoClient.end().catch(() => {})
    }
})
