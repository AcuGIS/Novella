CREATE TABLE metadata (
    id SERIAL PRIMARY KEY,
    gis_data_id INTEGER NOT NULL REFERENCES gis_data(id) ON DELETE CASCADE,
    metadata JSONB NOT NULL,
    metadata_standard VARCHAR(50) NOT NULL DEFAULT 'ISO 19115',
    metadata_version VARCHAR(20) NOT NULL DEFAULT '2018',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_gis_data_metadata UNIQUE (gis_data_id)
);

CREATE INDEX idx_metadata_gis_data_id ON metadata(gis_data_id);
CREATE INDEX idx_metadata_standard ON metadata(metadata_standard);
CREATE INDEX idx_metadata_json ON metadata USING GIN(metadata); 