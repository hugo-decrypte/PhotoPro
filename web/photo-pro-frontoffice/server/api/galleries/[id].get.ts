import pg from 'pg'

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    const config = useRuntimeConfig()

    const client = new pg.Client({
        host: config.dbHost,
        port: Number(config.dbPort),
        user: config.dbUser,
        password: config.dbPassword,
        database: config.dbName,
    })

    await client.connect()

    try {
        // Récupère la galerie
        const galleryResult = await client.query(`
      SELECT
        g.id,
        g.title,
        g.description,
        g.type,
        g.status,
        g.cover_photo_id,
        g.published_at
      FROM gallery g
      WHERE g.id = $1
        AND g.status = 'PUBLISHED'
    `, [id])

        if (galleryResult.rows.length === 0) {
            throw createError({ statusCode: 404, message: 'Galerie introuvable' })
        }

        const gallery = galleryResult.rows[0]

        // Si privée on retourne juste les infos sans les photos
        if (gallery.type === 'PRIVATE') {
            return { gallery, photos: null, private: true }
        }

        // Récupère les photos de la galerie
        const photosResult = await client.query(`
      SELECT
        gp.photo_id,
        gp.order,
        gp.added_at
      FROM gallery_photo gp
      WHERE gp.gallery_id = $1
      ORDER BY gp.order ASC
    `, [id])

        return {
            gallery,
            photos: photosResult.rows,
            private: false,
        }
    } finally {
        await client.end()
    }
})