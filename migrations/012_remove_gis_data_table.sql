-- First, remove the foreign key constraint from metadata table
ALTER TABLE metadata
DROP CONSTRAINT IF EXISTS unique_gis_data_metadata,
DROP COLUMN IF EXISTS gis_data_id;

-- Drop the index on gis_data_id
DROP INDEX IF EXISTS idx_metadata_gis_data_id;

-- Drop the gis_data table and its dependencies
DROP TABLE IF EXISTS gis_data CASCADE; 