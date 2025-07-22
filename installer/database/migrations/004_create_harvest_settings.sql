-- Create harvest_settings table
CREATE TABLE IF NOT EXISTS harvest_settings (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    wms_url TEXT NOT NULL,
    layers JSONB NOT NULL,
    interval_minutes INTEGER NOT NULL,
    last_run TIMESTAMP WITH TIME ZONE,
    next_run TIMESTAMP WITH TIME ZONE,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Add trigger to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_harvest_settings_updated_at
    BEFORE UPDATE ON harvest_settings
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column(); 