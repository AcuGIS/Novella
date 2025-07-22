-- Add thumbnail_path column to gis_files table
ALTER TABLE gis_files
ADD COLUMN IF NOT EXISTS thumbnail_path character varying(512);

-- Add comment to explain the column
COMMENT ON COLUMN gis_files.thumbnail_path IS 'Path to the dataset thumbnail image file'; 