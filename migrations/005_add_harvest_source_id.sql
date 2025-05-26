-- Add harvest_source_id column to oai_records table
ALTER TABLE oai_records ADD COLUMN IF NOT EXISTS harvest_source_id INTEGER REFERENCES harvest_sources(id) ON DELETE CASCADE;

-- Create an index on harvest_source_id for faster lookups
CREATE INDEX IF NOT EXISTS idx_oai_records_harvest_source_id ON oai_records(harvest_source_id);

-- Update existing records to link them to their harvest sources based on the dataset title
DO $$
DECLARE
    record RECORD;
    source_id INTEGER;
BEGIN
    FOR record IN SELECT r.id, r.dataset_id, d.title, h.id as harvest_id 
                 FROM oai_records r 
                 JOIN datasets d ON r.dataset_id = d.id 
                 JOIN harvest_sources h ON d.title LIKE '%' || h.url || '%' LOOP
        UPDATE oai_records 
        SET harvest_source_id = record.harvest_id 
        WHERE id = record.id;
    END LOOP;
END $$; 