-- Galleries
INSERT INTO gallery (id, photographer_id, title, description, layout, status, type, cover_photo_id, created_at, published_at) VALUES
    ('5578f6b7-8870-4faa-927e-4a92349efc1c',	'66a087dd-177b-4502-9ea8-16ace85170f2',	'Winter',	'Photos du photographe Vincent Munier',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:20:22+00',	NULL),
    ('b9c5a351-eb86-4065-b8e1-9743595be455',	'55b6836c-1cc3-44d1-b091-06403ba4ab5c',	'La Mer',	'Collection de photos sublime de la mer',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:24:13+00',	NULL),
    ('3ef5d836-fe55-4a03-a9fd-c43acf446a7b',	'31ce50cb-83e8-4f11-bd4a-ba694b1756c7',	'La Forêt des Vosges',	'',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:25:18+00',	NULL),
    ('412fb78f-46b7-4461-b3ce-ad18a58b6907',	'271a3a99-7807-4b12-ac65-0dbc0a6990f3',	'Desert',	'',	'GRID',	'PUBLISHED',	'PRIVATE',	NULL,	'2026-04-06 15:32:24+00',	NULL),
    ('e0694e60-7556-440b-90e7-cd294d35224f',	'c33883f3-ceea-4410-b205-3e5df613cc24',	'Pont',	'Photo de pont',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:20:22+00',	NULL),
    ('60e7de48-13ce-441b-a1b6-e186d650d075',	'0a1698eb-b947-40f9-b372-ff34f439fc29',	'Lego',	'Magasin LEGO Magnifique',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:24:13+00',	NULL),
    ('1ec9e6b8-d2d8-4c11-a94c-54665e265f10',	'921e5712-69a0-4905-9fcd-78ebe158f889',	'Papier',	'',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:25:18+00',	NULL),
    ('5f93a677-44c3-4011-9159-6bbbbd4e1249',	'50560519-b419-443f-bd70-c62f32cb853c',	'Japon',	'Une architecture magnifique',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:20:22+00',	NULL),
    ('a4cb6df0-c4ca-4553-b399-3c9f75e2e4ab',	'a32c18d7-57db-48c8-b10a-3b1b30a18357',	'Plot',	'Des plot',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:24:13+00',	NULL),
    ('ec34dcbd-98da-4411-85e1-ca49fdba6853',	'f424a23f-0b6a-49fc-8cb6-9f61f2915410',	'Voiture',	'On aime les voitures ICI',	'GRID',	'PUBLISHED',	'PUBLIC',	NULL,	'2026-04-06 15:25:18+00',	NULL);

-- Accès galerie privée
INSERT INTO private_gallery_access (gallery_id, client_name, client_email, client_phone, access_code, direct_url) VALUES
    ('412fb78f-46b7-4461-b3ce-ad18a58b6907',	'cazottes',	'cazottes.alexandre@mail.com',	NULL,	'9041416A',	'/galeries/412fb78f-46b7-4461-b3ce-ad18a58b6907/privee');

-- Photos dans les galeries
INSERT INTO gallery_photo (gallery_id, photo_id, "order", added_at) VALUES
    ('5578f6b7-8870-4faa-927e-4a92349efc1c',	'1788605c-e4b2-4a59-88ab-bb37ba4addb1',	1,	'2026-04-06 16:06:52.096582+00'),
    ('5578f6b7-8870-4faa-927e-4a92349efc1c',	'd657fd5d-ad4c-45b0-94b4-4317901c956f',	2,	'2026-04-06 16:06:52.096582+00'),
    ('5578f6b7-8870-4faa-927e-4a92349efc1c',	'0442d804-21d5-4f97-8f4f-45fdff6a2483',	3,	'2026-04-06 16:06:52.096582+00'),
    ('5578f6b7-8870-4faa-927e-4a92349efc1c',	'd451141e-aff5-4823-9560-30ce3f087df1',	4,	'2026-04-06 16:06:52.096582+00'),
    ('b9c5a351-eb86-4065-b8e1-9743595be455',	'1543b257-d086-42d4-b4da-4aeeb7985e8b',	1,	'2026-04-06 16:09:06.468734+00'),
    ('b9c5a351-eb86-4065-b8e1-9743595be455',	'76f26a04-57d5-42ff-b36e-d523543d0296',	2,	'2026-04-06 16:09:06.468734+00'),
    ('3ef5d836-fe55-4a03-a9fd-c43acf446a7b',	'19d5027a-64d2-492f-a015-0174059bd8a0',	1,	'2026-04-06 16:10:35.415196+00'),
    ('3ef5d836-fe55-4a03-a9fd-c43acf446a7b',	'ea235331-09c0-4666-a4ee-aabbd759acf0',	2,	'2026-04-06 16:10:35.415196+00'),
    ('3ef5d836-fe55-4a03-a9fd-c43acf446a7b',	'98983d45-d2c4-498f-ac75-46161e36cb48',	3,	'2026-04-06 16:10:35.415196+00'),
    ('412fb78f-46b7-4461-b3ce-ad18a58b6907',	'6faf9399-acc8-4fc0-a6d9-bb8163089ab2',	1,	'2026-04-06 16:12:09.685781+00'),
    ('412fb78f-46b7-4461-b3ce-ad18a58b6907',	'e7ebc33a-dcef-4e9e-8e83-c2909e6afcb0',	2,	'2026-04-06 16:12:09.685781+00'),
    ('e0694e60-7556-440b-90e7-cd294d35224f',	'54ad4060-c851-4941-b985-46e874e23a34',	1,	'2026-04-06 16:12:09.685781+00'),
    ('60e7de48-13ce-441b-a1b6-e186d650d075',	'80d3033f-e5fe-4598-86c7-f185cead530d',	1,	'2026-04-06 16:12:09.685781+00'),
    ('1ec9e6b8-d2d8-4c11-a94c-54665e265f10',	'500debdc-eca0-47b5-b40b-994cdcf76df9',	1,	'2026-04-06 16:12:09.685781+00'),
    ('5f93a677-44c3-4011-9159-6bbbbd4e1249',	'5767344f-5089-456a-b4ac-d5b2f969815e',	1,	'2026-04-06 16:12:09.685781+00'),
    ('a4cb6df0-c4ca-4553-b399-3c9f75e2e4ab',	'41a271ca-8fed-4d54-ba75-66365ce18b57',	1,	'2026-04-06 16:12:09.685781+00'),
    ('ec34dcbd-98da-4411-85e1-ca49fdba6853',	'97d5e465-01b7-4b9c-b4b8-49083a3d57e2',	1,	'2026-04-06 16:12:09.685781+00');

-- Commentaires
INSERT INTO comment (id, photo_id, gallery_id, author_name, content, created_at) VALUES
    (gen_random_uuid(), '1788605c-e4b2-4a59-88ab-bb37ba4addb1', '5578f6b7-8870-4faa-927e-4a92349efc1c', 'arman',    'Le loup est magnifique',          '2026-04-06 16:17:09.685781+00'),
    (gen_random_uuid(), 'd657fd5d-ad4c-45b0-94b4-4317901c956f', '5578f6b7-8870-4faa-927e-4a92349efc1c', 'wiem',  'Superbe photo', '2026-04-06 16:18:09.685781+00'),
    (gen_random_uuid(), '1543b257-d086-42d4-b4da-4aeeb7985e8b', 'b9c5a351-eb86-4065-b8e1-9743595be455', NULL,      'Très belle perspective',     '2026-04-06 16:19:09.685781+00'),
    (gen_random_uuid(), '19d5027a-64d2-492f-a015-0174059bd8a0', '3ef5d836-fe55-4a03-a9fd-c43acf446a7b', NULL,   'Merci infiniment', '2026-04-06 16:20:09.685781+00');