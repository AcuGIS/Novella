-- Add status column to oai_records table
ALTER TABLE oai_records ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'active';

-- Create oai_logs table
CREATE TABLE oai_logs (
    id SERIAL PRIMARY KEY,
    harvest_source_id INTEGER NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (harvest_source_id) REFERENCES harvest_sources(id) ON DELETE CASCADE
);

-- Add indexes for better performance
CREATE INDEX idx_oai_records_status ON oai_records(status);
CREATE INDEX idx_oai_logs_harvest_source ON oai_logs(harvest_source_id);
CREATE INDEX idx_oai_logs_created_at ON oai_logs(created_at); 