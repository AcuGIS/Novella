-- Add columns from gis_data to datasets table
ALTER TABLE datasets
ADD COLUMN is_public BOOLEAN NOT NULL DEFAULT false,
ADD COLUMN status VARCHAR(50) NOT NULL DEFAULT 'draft',
ADD COLUMN created_by INTEGER REFERENCES users(id);

-- Create indexes
CREATE INDEX idx_datasets_status ON datasets(status);
CREATE INDEX idx_datasets_created_by ON datasets(created_by);

-- Add trigger for updated_at
CREATE TRIGGER update_datasets_updated_at
    BEFORE UPDATE ON datasets
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Migrate data from gis_data to datasets
INSERT INTO datasets (
    title,
    description,
    file_path,
    file_type,
    file_size,
    created_by,
    created_at,
    updated_at,
    is_public,
    status,
    spatial_extent
)
SELECT 
    title,
    description,
    file_path,
    file_type,
    file_size,
    created_by,
    created_at,
    updated_at,
    is_public,
    status,
    ST_ConvexHull(geometry)::geometry(Polygon,4326) as spatial_extent
FROM gis_data
ON CONFLICT (id) DO NOTHING; 