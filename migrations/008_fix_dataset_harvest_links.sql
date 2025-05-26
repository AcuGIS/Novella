-- First, unlink all datasets from harvest sources
UPDATE oai_records SET harvest_source_id = NULL;

-- Then, link datasets to their harvest sources based on creation time
UPDATE oai_records r
SET harvest_source_id = h.id
FROM harvest_sources h
WHERE r.dataset_id IN (
    SELECT d.id 
    FROM datasets d 
    WHERE d.created_at >= h.created_at
);

-- Add harvest_source_id to oai_records table
ALTER TABLE oai_records
ADD COLUMN harvest_source_id INTEGER REFERENCES harvest_sources(id) ON DELETE CASCADE;

-- Create index for harvest_source_id
CREATE INDEX idx_oai_records_harvest_source_id ON oai_records(harvest_source_id);

-- Update existing records to link to harvest sources
UPDATE oai_records r
SET harvest_source_id = (
    SELECT id 
    FROM harvest_sources h 
    WHERE h.url = (
        SELECT url 
        FROM harvest_sources 
        WHERE id = (
            SELECT harvest_source_id 
            FROM oai_records 
            WHERE id = r.id
        )
    )
    LIMIT 1
)
WHERE harvest_source_id IS NULL; 