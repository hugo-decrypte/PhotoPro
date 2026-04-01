CREATE TABLE photo (
                       id                  UUID PRIMARY KEY DEFAULT gen_random_uuid(),
                       photographer_id     UUID NOT NULL,
                       title               TEXT,
                       mime_type           TEXT NOT NULL,
                       size_bytes          INTEGER NOT NULL,
                       original_filename   TEXT NOT NULL,
                       s3_key              TEXT NOT NULL,
                       uploaded_at         TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT now()
);

-- INDEXES
CREATE INDEX idx_photo_photographer ON photo(photographer_id);