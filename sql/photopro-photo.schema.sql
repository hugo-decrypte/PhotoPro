CREATE TABLE photo (
       id UUID PRIMARY KEY,
       photographer_id UUID NOT NULL,
       mime_type VARCHAR(100) NOT NULL,
       size_bytes BIGINT NOT NULL,
       original_filename VARCHAR(255) NOT NULL,
       s3_key VARCHAR(512) NOT NULL,
       uploaded_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
       title VARCHAR(255) NULL
);