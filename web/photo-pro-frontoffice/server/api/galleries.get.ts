import pg from 'pg'

export default defineEventHandler(async () => {
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

        const result = await galleryClient.query(`
            SELECT
                g.id,
                g.title,
                g.description,
                g.cover_photo_id,
                g.published_at,
                COUNT(gp.photo_id) as photo_count
            FROM gallery g
            LEFT JOIN gallery_photo gp ON gp.gallery_id = g.id
            WHERE g.status = 'PUBLISHED'
              AND g.type = 'PUBLIC'
            GROUP BY g.id
            ORDER BY g.published_at DESC NULLS LAST
        `)

        const galleries = result.rows

        // Récupère les cover_photo_id non null
        const coverIds = galleries
            .map(g => g.cover_photo_id)
            .filter(Boolean)

        if (coverIds.length > 0) {
            await photoClient.connect()

            const photosResult = await photoClient.query(`
                SELECT id, s3_key FROM photo
                WHERE id = ANY($1::uuid[])
            `, [coverIds])

            const photoMap = new Map(photosResult.rows.map(p => [p.id, p]))

            // Ajoute l'URL de cover à chaque galerie
            return galleries.map(g => ({
                ...g,
                cover_photo_url: g.cover_photo_id
                    ? `${config.public.s3Endpoint}/${photoMap.get(g.cover_photo_id)?.s3_key}`
                    : null
            }))
        }

        return galleries

    } catch (err) {
        console.error(err)
        throw createError({ statusCode: 500, message: 'Erreur récupération galeries' })
    } finally {
        await galleryClient.end().catch(() => {})
        await photoClient.end().catch(() => {})
    }
})