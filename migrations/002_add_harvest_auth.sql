-- Add username and password fields to harvest_sources table
ALTER TABLE harvest_sources
ADD COLUMN username VARCHAR(255),
ADD COLUMN password VARCHAR(255);

-- Create index for username
CREATE INDEX IF NOT EXISTS idx_harvest_sources_username ON harvest_sources(username); 