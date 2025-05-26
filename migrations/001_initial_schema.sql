-- Create datasets table
CREATE TABLE IF NOT EXISTS datasets (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(255),
    file_type VARCHAR(50),
    file_size BIGINT,
    spatial_extent GEOMETRY(POLYGON, 4326),
    temporal_extent TSTZRANGE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create metadata table for ISO 19115
CREATE TABLE IF NOT EXISTS metadata (
    id SERIAL PRIMARY KEY,
    dataset_id INTEGER REFERENCES datasets(id) ON DELETE CASCADE,
    metadata_standard VARCHAR(50) NOT NULL DEFAULT 'ISO 19115',
    metadata_version VARCHAR(20) NOT NULL DEFAULT '2018',
    metadata_xml XML,
    quality_score FLOAT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create keywords table
CREATE TABLE IF NOT EXISTS keywords (
    id SERIAL PRIMARY KEY,
    dataset_id INTEGER REFERENCES datasets(id) ON DELETE CASCADE,
    keyword VARCHAR(100) NOT NULL,
    thesaurus VARCHAR(255),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create contacts table
CREATE TABLE IF NOT EXISTS contacts (
    id SERIAL PRIMARY KEY,
    dataset_id INTEGER REFERENCES datasets(id) ON DELETE CASCADE,
    role VARCHAR(50) NOT NULL,
    organization VARCHAR(255),
    individual_name VARCHAR(255),
    position_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(50),
    address TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create distribution table
CREATE TABLE IF NOT EXISTS distribution (
    id SERIAL PRIMARY KEY,
    dataset_id INTEGER REFERENCES datasets(id) ON DELETE CASCADE,
    format VARCHAR(100) NOT NULL,
    url VARCHAR(255),
    protocol VARCHAR(50),
    name VARCHAR(255),
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_datasets_title ON datasets(title);
CREATE INDEX IF NOT EXISTS idx_datasets_spatial_extent ON datasets USING GIST(spatial_extent);
CREATE INDEX IF NOT EXISTS idx_metadata_dataset_id ON metadata(dataset_id);
CREATE INDEX IF NOT EXISTS idx_keywords_dataset_id ON keywords(dataset_id);
CREATE INDEX IF NOT EXISTS idx_contacts_dataset_id ON contacts(dataset_id);
CREATE INDEX IF NOT EXISTS idx_distribution_dataset_id ON distribution(dataset_id);

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create triggers for updated_at
CREATE TRIGGER update_datasets_updated_at
    BEFORE UPDATE ON datasets
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_metadata_updated_at
    BEFORE UPDATE ON metadata
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_contacts_updated_at
    BEFORE UPDATE ON contacts
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_distribution_updated_at
    BEFORE UPDATE ON distribution
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column(); 