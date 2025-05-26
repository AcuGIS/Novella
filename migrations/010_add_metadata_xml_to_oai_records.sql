-- Add metadata_xml column to oai_records table
ALTER TABLE oai_records ADD COLUMN metadata_xml XML;

-- Create an index on metadata_xml for faster lookups
CREATE INDEX IF NOT EXISTS idx_oai_records_metadata_xml ON oai_records USING gin (metadata_xml); 