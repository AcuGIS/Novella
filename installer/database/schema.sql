-- Create extension for UUID support
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS postgis WITH SCHEMA public;

-- Create tables for ISO 19115 + INSPIRE metadata
CREATE TABLE IF NOT EXISTS metadata_records (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title character varying(255) NOT NULL,
    abstract text NOT NULL,
    purpose text,
    keywords text[],
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    wms_url character varying(512),
    wms_layer character varying(255),
    contact_org character varying(255),
    conformity character varying(255),
    service_url character varying(512),
    metadata_date date,
    metadata_language character varying(10),
    metadata_point_of_contact character varying(255),
    spatial_resolution character varying(255),
    resource_type character varying(50),
    lineage text,
    data_format character varying(255)[],
    distribution_url character varying(512),
    coupled_resource character varying(255),
    metadata_poc_organization character varying(255),
    metadata_poc_email character varying(255),
    metadata_poc_role character varying(255),
    resource_identifier character varying(255),
    maintenance_frequency character varying(50),
    character_set character varying(50),
    topic_id uuid,
    inspire_theme_id uuid,
    is_public boolean DEFAULT false
);

-- Add comments to explain the columns
COMMENT ON COLUMN metadata_records.contact_org IS 'Organization responsible for metadata';
COMMENT ON COLUMN metadata_records.conformity IS 'Specification conformity (e.g., INSPIRE spec)';
COMMENT ON COLUMN metadata_records.service_url IS 'URL of the spatial data service';
COMMENT ON COLUMN metadata_records.metadata_date IS 'Date the metadata record was created or updated';
COMMENT ON COLUMN metadata_records.metadata_language IS 'Language used for the metadata (e.g., eng)';
COMMENT ON COLUMN metadata_records.metadata_point_of_contact IS 'Contact person/organization for metadata';
COMMENT ON COLUMN metadata_records.spatial_resolution IS 'Scale or equivalent resolution of data';
COMMENT ON COLUMN metadata_records.resource_type IS 'Type of resource (e.g., dataset, service)';
COMMENT ON COLUMN metadata_records.lineage IS 'Statement about data quality and origin';
COMMENT ON COLUMN metadata_records.data_format IS 'Format(s) in which the data is available';
COMMENT ON COLUMN metadata_records.distribution_url IS 'Download or access URL';
COMMENT ON COLUMN metadata_records.coupled_resource IS 'Associated dataset if metadata is for a service'; 

CREATE TABLE public.topics (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    topic character varying(255) NOT NULL,
    description text,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE public.keywords (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    keyword character varying(255) NOT NULL,
    description text,
    created_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp with time zone DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS citations (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    citation_date DATE NOT NULL,
    responsible_org VARCHAR(255) NOT NULL,
    responsible_person VARCHAR(255),
    role VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS contacts (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    organization VARCHAR(255) NOT NULL,
    person VARCHAR(255),
    role VARCHAR(50) NOT NULL,
    email VARCHAR(255),
    phone VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS geographic_extents (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    west_longitude DECIMAL(10,6) NOT NULL,
    east_longitude DECIMAL(10,6) NOT NULL,
    south_latitude DECIMAL(10,6) NOT NULL,
    north_latitude DECIMAL(10,6) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS temporal_extents (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    start_date DATE,
    end_date DATE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS spatial_representations (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    coordinate_system VARCHAR(50),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS constraints (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    use_constraints TEXT,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    access_constraints text,
    use_limitation text
);

CREATE TABLE IF NOT EXISTS inspire_metadata (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    point_of_contact_org VARCHAR(255),
    conformity_result VARCHAR(50),
    spatial_data_service_url VARCHAR(255),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS gis_files (
    id UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    metadata_id UUID REFERENCES metadata_records(id) ON DELETE CASCADE,
    file_name VARCHAR(255) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size BIGINT NOT NULL,
    file_path VARCHAR(512) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Create function to update updated_at timestamp
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

-- Create trigger for metadata_records
CREATE TRIGGER update_metadata_records_updated_at
    BEFORE UPDATE ON metadata_records
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();
