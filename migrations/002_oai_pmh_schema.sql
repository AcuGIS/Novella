-- Create OAI-PMH sets table
CREATE TABLE IF NOT EXISTS oai_sets (
    id SERIAL PRIMARY KEY,
    set_spec VARCHAR(255) NOT NULL UNIQUE,
    set_name VARCHAR(255) NOT NULL,
    set_description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create OAI-PMH records table
CREATE TABLE IF NOT EXISTS oai_records (
    id SERIAL PRIMARY KEY,
    dataset_id INTEGER REFERENCES datasets(id) ON DELETE CASCADE,
    oai_identifier VARCHAR(255) NOT NULL UNIQUE,
    datestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted BOOLEAN NOT NULL DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Create OAI-PMH record sets mapping table
CREATE TABLE IF NOT EXISTS oai_record_sets (
    record_id INTEGER REFERENCES oai_records(id) ON DELETE CASCADE,
    set_id INTEGER REFERENCES oai_sets(id) ON DELETE CASCADE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (record_id, set_id)
);

-- Create indexes for OAI-PMH tables
CREATE INDEX IF NOT EXISTS idx_oai_records_dataset_id ON oai_records(dataset_id);
CREATE INDEX IF NOT EXISTS idx_oai_records_identifier ON oai_records(oai_identifier);
CREATE INDEX IF NOT EXISTS idx_oai_records_datestamp ON oai_records(datestamp);
CREATE INDEX IF NOT EXISTS idx_oai_record_sets_record_id ON oai_record_sets(record_id);
CREATE INDEX IF NOT EXISTS idx_oai_record_sets_set_id ON oai_record_sets(set_id);

-- Create trigger for oai_sets updated_at
CREATE TRIGGER update_oai_sets_updated_at
    BEFORE UPDATE ON oai_sets
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Create trigger for oai_records updated_at
CREATE TRIGGER update_oai_records_updated_at
    BEFORE UPDATE ON oai_records
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- Insert default OAI-PMH set
INSERT INTO oai_sets (set_spec, set_name, set_description)
VALUES ('default', 'Default Set', 'Default OAI-PMH set containing all records')
ON CONFLICT (set_spec) DO NOTHING; 