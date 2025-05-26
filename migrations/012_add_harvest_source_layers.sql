-- Create harvest_source_layers table
CREATE TABLE harvest_source_layers (
    id SERIAL PRIMARY KEY,
    harvest_source_id INTEGER NOT NULL REFERENCES harvest_sources(id) ON DELETE CASCADE,
    layer_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(harvest_source_id, layer_name)
);

-- Create indexes
CREATE INDEX idx_harvest_source_layers_source_id ON harvest_source_layers(harvest_source_id);
CREATE INDEX idx_harvest_source_layers_layer_name ON harvest_source_layers(layer_name);

-- Add trigger for updated_at
CREATE TRIGGER update_harvest_source_layers_updated_at
    BEFORE UPDATE ON harvest_source_layers
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column(); 