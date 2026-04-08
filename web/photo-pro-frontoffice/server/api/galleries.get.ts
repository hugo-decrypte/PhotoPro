import pg from 'pg'

export default defineEventHandler(async () => {
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
        const result = await client.query(`
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

        return result.rows

    } catch (err) {
        console.error(err)
        throw createError({
            statusCode: 500,
            message: 'Erreur récupération galeries'
        })
    } finally {
        await client.end()
    }
})