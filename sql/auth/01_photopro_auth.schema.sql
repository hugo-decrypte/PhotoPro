CREATE TABLE photographer (
                              id             UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                              first_name     TEXT NOT NULL,
                              name           TEXT NOT NULL,
                              email          TEXT NOT NULL UNIQUE,
                              password_hash  TEXT NOT NULL,
                              pseudo         TEXT NOT NULL UNIQUE,
                              created_at     TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-- INDEXES
CREATE INDEX idx_photographer_email ON photographer(email);
CREATE INDEX idx_photographer_pseudo ON photographer(pseudo);