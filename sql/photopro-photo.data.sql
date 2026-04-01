-- Données d'exemple pour la table photo
INSERT INTO photo (id, photographer_id, mime_type, size_bytes, original_filename, s3_key, uploaded_at, title) VALUES
('440e8400-e29b-41d4-a716-446655440001', 'd975aca7-50c5-3d16-b211-cf7d302cba50', 'image/jpeg', 1048576, 'portrait_pro.jpg', 'users/denis/440e8400-e29b-41d4-a716-446655440001.jpg', CURRENT_TIMESTAMP, 'Portrait Professionnel'),
('440e8400-e29b-41d4-a716-446655440002', 'd975aca7-50c5-3d16-b211-cf7d302cba50', 'image/png', 2048576, 'paysage_nuit.png', 'users/denis/440e8400-e29b-41d4-a716-446655440002.png', CURRENT_TIMESTAMP, 'Paysage nocturne'),
('440e8400-e29b-41d4-a716-446655440003', 'd975aca7-50c5-3d16-b211-cf7d302cba50', 'image/jpeg', 1572864, 'mariage_01.jpg', 'users/denis/440e8400-e29b-41d4-a716-446655440003.jpg', CURRENT_TIMESTAMP, 'Mariage - Cérémonie');
