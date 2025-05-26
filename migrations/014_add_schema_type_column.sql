-- Add schema_type column to metadata_templates table
ALTER TABLE metadata_templates 
ADD COLUMN schema_type VARCHAR(50) NOT NULL DEFAULT 'ISO19115';

-- Update existing template to have ISO19115 schema type
UPDATE metadata_templates 
SET schema_type = 'ISO19115' 
WHERE id = 1; 