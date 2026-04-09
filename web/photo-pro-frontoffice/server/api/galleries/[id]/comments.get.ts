import pg from 'pg';

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id');
    const config = useRuntimeConfig();

    const client = new pg.Client({
        host: config.dbHost,
        port: Number(config.dbPort),
        user: config.dbUser,
        password: config.dbPassword,
        database: config.dbName,
    });

    try {
        await client.connect();

        // 1. Vérifie que la galerie existe et est publiée
        const galleryResult = await client.query(`
            SELECT id FROM gallery
            WHERE id = $1 AND status = 'PUBLISHED'
        `, [id]);

        if (galleryResult.rows.length === 0) {
            throw createError({
                statusCode: 404,
                message: 'Galerie introuvable ou non publiée'
            });
        }

        // 2. Récupère les commentaires de la galerie, triés par date
        const commentsResult = await client.query(`
            SELECT
                id,
                photo_id,
                author_name,
                content,
                created_at
            FROM comment
            WHERE gallery_id = $1
            ORDER BY created_at DESC
        `, [id]);

        // 3. Retourne le format attendu
        return {
            success: true,
            data: commentsResult.rows,
            count: commentsResult.rows.length
        };

    } catch (err) {
        console.error('Erreur récupération commentaires:', err);
        throw createError({
            statusCode: 500,
            message: 'Erreur serveur'
        });
    } finally {
        await client.end().catch(() => {});
    }
});