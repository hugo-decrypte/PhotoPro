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
        g.created_at,
        g.published_at,
        COUNT(gp.photo_id) AS photo_count
      FROM gallery g
      LEFT JOIN gallery_photo gp ON gp.gallery_id = g.id
      WHERE g.status = 'PUBLISHED'
        AND g.type = 'PUBLIC'
      GROUP BY g.id
      ORDER BY g.published_at DESC
    `)
        return result.rows
    } finally {
        await client.end()
    }
})