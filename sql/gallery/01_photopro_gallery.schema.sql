-- ENUMS
CREATE TYPE gallery_status AS ENUM ('DRAFT', 'PUBLISHED', 'UNPUBLISHED');
CREATE TYPE gallery_type   AS ENUM ('PUBLIC', 'PRIVATE');
CREATE TYPE layout_type    AS ENUM ('GRID', 'MASONRY', 'SLIDESHOW');

CREATE TABLE gallery (
                         id               UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                         photographer_id  UUID NOT NULL,
                         title            TEXT NOT NULL,
                         description      TEXT,
                         layout           layout_type NOT NULL DEFAULT 'GRID',
                         status           gallery_status NOT NULL DEFAULT 'DRAFT',
                         type             gallery_type NOT NULL,
                         cover_photo_id   UUID,
                         created_at       TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),
                         published_at     TIMESTAMP WITH TIME ZONE
);

CREATE TABLE private_gallery_access (
                                        gallery_id    UUID PRIMARY KEY,
                                        client_name   TEXT NOT NULL,
                                        client_email  TEXT NOT NULL,
                                        client_phone  TEXT,
                                        access_code   TEXT NOT NULL UNIQUE,
                                        direct_url    TEXT NOT NULL UNIQUE,

                                        CONSTRAINT fk_private_gallery
                                            FOREIGN KEY (gallery_id)
                                                REFERENCES gallery(id)
                                                ON DELETE CASCADE
);

CREATE TABLE gallery_photo (
                               gallery_id  UUID NOT NULL,
                               photo_id    UUID NOT NULL,
                               "order"     INTEGER NOT NULL,
                               added_at    TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

                               PRIMARY KEY (gallery_id, photo_id),

                               CONSTRAINT fk_gp_gallery
                                   FOREIGN KEY (gallery_id)
                                       REFERENCES gallery(id)
                                       ON DELETE CASCADE
);

CREATE TABLE comment (
                         id          UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                         photo_id    UUID NOT NULL,
                         gallery_id  UUID NOT NULL,
                         author_name TEXT,
                         content     TEXT NOT NULL,
                         created_at  TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now(),

                         CONSTRAINT fk_comment_gallery
                             FOREIGN KEY (gallery_id)
                                 REFERENCES gallery(id)
                                 ON DELETE CASCADE
);

-- INDEXES
CREATE INDEX idx_gallery_photographer  ON gallery(photographer_id);
CREATE INDEX idx_gallery_status        ON gallery(status);
CREATE INDEX idx_gallery_photo_gallery ON gallery_photo(gallery_id);
CREATE INDEX idx_comment_gallery       ON comment(gallery_id);
CREATE INDEX idx_comment_photo         ON comment(photo_id);