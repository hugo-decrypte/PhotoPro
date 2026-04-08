import pg from 'pg'

export default defineEventHandler(async (event) => {
    const id = getRouterParam(event, 'id')
    const config = useRuntimeConfig()
    const query = getQuery(event)
    const code = query.code

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

        // GALERIE
        const galleryResult = await galleryClient.query(`
          SELECT id, title, description, type, status, cover_photo_id, published_at
          FROM gallery
          WHERE id = $1 AND status = 'PUBLISHED'
        `, [id])

        if (galleryResult.rows.length === 0) {
            throw createError({ statusCode: 404, message: 'Galerie introuvable' })
        }

        let gallery = galleryResult.rows[0]

        if (gallery.type === 'PRIVATE') {
            if (!code) {
                return { gallery, photos: null, private: true }
            }

            const privateCheck = await galleryClient.query(
                'SELECT access_code FROM private_gallery_access WHERE gallery_id = $1 AND access_code = $2',
                [id, code]
            )

            if (privateCheck.rows.length === 0) {
                throw createError({ statusCode: 403, message: 'Code invalide' })
            }
        }

        // LIAISONS PHOTOS
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

        // DB PHOTO
        await photoClient.connect()

        const photosResult = await photoClient.query(`
            SELECT id, title, s3_key, mime_type
            FROM photo
            WHERE id = ANY($1::uuid[])
        `, [photoIds])

        const photoMap = new Map(photosResult.rows.map(p => [p.id, p]))

        // FUSION ET URLS
        const photos = gpResult.rows
            .map(gp => {
                const photoData = photoMap.get(gp.photo_id)
                if (!photoData) return null
                return {
                    ...photoData,
                    order: gp.order,
                    url: `${config.public.s3Endpoint}/${photoData.s3_key}`
                }
            })
            .filter(Boolean)

        // COVER URL
        if (gallery.cover_photo_id) {
            const coverPhoto = photoMap.get(gallery.cover_photo_id)
            if (coverPhoto) {
                gallery = {
                    ...gallery,
                    cover_photo_url: `${config.public.s3Endpoint}/${coverPhoto.s3_key}`
                }
            }
        }

        console.log('Photos from DB:', photosResult.rows);


        return { gallery, photos, private: false }

    } catch (err: any) {
        console.error('API ERROR:', err)
        throw createError({
            statusCode: err.statusCode || 500,
            message: err.message || 'Erreur serveur galerie'
        })
    } finally {
        await galleryClient.end().catch(() => {})
        await photoClient.end().catch(() => {})
    }
})