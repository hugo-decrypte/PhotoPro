import pg from 'pg';

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id');
    const photoId = getRouterParam(event, 'photoId');
    const body = await readBody(event);
    const config = useRuntimeConfig();

    if (!body?.content?.trim()) {
        throw createError({ statusCode: 400, message: 'Contenu manquant' });
    }

    const client = new pg.Client({
        host: config.dbHost,
        port: Number(config.dbPort),
        user: config.dbUser,
        password: config.dbPassword,
        database: config.dbName,
    });

    await client.connect();

    try {
        await client.query(
            `
      INSERT INTO comment (id, gallery_id, photo_id, author_name, content, created_at)
      VALUES (gen_random_uuid(), $1, $2, $3, $4, NOW())
      `,
            [
                id,
                photoId === '00000000-0000-0000-0000-000000000000' ? null : photoId,
                body.author_name?.trim() || 'Anonyme',
                body.content.trim(),
            ]
        );
        return { success: true };
    } finally {
        await client.end().catch(() => {});
    }
});