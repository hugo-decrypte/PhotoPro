CREATE TABLE galleries (
    id UUID PRIMARY KEY,
    photographer_id UUID NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'draft',
    type VARCHAR(20) NOT NULL CHECK (type IN ('public', 'private')),
    cover_photo_id UUID NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    published_at TIMESTAMP NULL
);

CREATE TABLE private_gallery_access (
    gallery_id UUID PRIMARY KEY REFERENCES galleries(id) ON DELETE CASCADE,
    client_name VARCHAR(255) NULL,
    client_email VARCHAR(255) NOT NULL,
    client_phone VARCHAR(50) NULL,
    access_code VARCHAR(32) NOT NULL,
    direct_url VARCHAR(255) NOT NULL
);

CREATE TABLE gallery_photos (
    gallery_id UUID NOT NULL REFERENCES galleries(id) ON DELETE CASCADE,
    photo_id UUID NOT NULL,
    display_order INTEGER NOT NULL DEFAULT 1,
    added_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (gallery_id, photo_id)
);