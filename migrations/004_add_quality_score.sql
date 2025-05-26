-- Add quality_score column to metadata table
ALTER TABLE metadata ADD COLUMN IF NOT EXISTS quality_score FLOAT;

-- Create an index on quality_score for faster sorting and filtering
CREATE INDEX IF NOT EXISTS idx_metadata_quality_score ON metadata(quality_score);

-- Update existing records to calculate quality scores
DO $$
DECLARE
    record RECORD;
BEGIN
    FOR record IN SELECT id, metadata_xml FROM metadata WHERE quality_score IS NULL LOOP
        UPDATE metadata 
        SET quality_score = (
            SELECT calculate_quality_score(metadata_xml::text)
            FROM metadata 
            WHERE id = record.id
        )
        WHERE id = record.id;
    END LOOP;
END $$; 