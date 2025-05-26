-- Enable PostGIS extension if not already enabled
CREATE EXTENSION IF NOT EXISTS postgis;

CREATE TABLE gis_data (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    geometry GEOMETRY(GEOMETRY, 4326),
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    file_size BIGINT,
    created_by INTEGER REFERENCES users(id),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    is_public BOOLEAN NOT NULL DEFAULT false,
    status VARCHAR(50) NOT NULL DEFAULT 'draft'
);

CREATE INDEX idx_gis_data_title ON gis_data(title);
CREATE INDEX idx_gis_data_geometry ON gis_data USING GIST(geometry);
CREATE INDEX idx_gis_data_created_by ON gis_data(created_by);
CREATE INDEX idx_gis_data_status ON gis_data(status); 