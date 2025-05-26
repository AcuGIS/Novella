-- Create function to calculate metadata quality score
CREATE OR REPLACE FUNCTION calculate_quality_score(metadata_xml xml)
RETURNS float AS $$
DECLARE
    score float := 0.0;
    max_score float := 100.0;
    weights jsonb := '{
        "identification": {
            "title": 5,
            "abstract": 5,
            "purpose": 3,
            "resource_type": 2,
            "language": 2,
            "keywords": 3
        },
        "extent": {
            "spatial": 10,
            "temporal": 5
        },
        "responsible_parties": {
            "point_of_contact": 5,
            "publisher": 3,
            "author": 3
        },
        "spatial_reference": {
            "crs": 5,
            "crs_type": 3
        },
        "data_quality": {
            "lineage": 5,
            "conformity": 3
        },
        "constraints": {
            "use_constraints": 5,
            "access_constraints": 5,
            "license": 5
        },
        "distribution": {
            "url": 5,
            "format": 3,
            "distributor": 3
        },
        "maintenance": {
            "update_frequency": 3,
            "maintenance_notes": 2
        }
    }'::jsonb;
BEGIN
    -- Identification Information
    IF array_length(xpath('//*[local-name()="title"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'title')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="abstract"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'abstract')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="purpose"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'purpose')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="resourceType"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'resource_type')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="language"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'language')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="keyword"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'identification'->>'keywords')::float;
    END IF;

    -- Extent Information
    IF array_length(xpath('//*[local-name()="EX_GeographicBoundingBox"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'extent'->>'spatial')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="temporalElement"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'extent'->>'temporal')::float;
    END IF;

    -- Responsible Parties
    IF array_length(xpath('//*[local-name()="pointOfContact"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'responsible_parties'->>'point_of_contact')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="publisher"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'responsible_parties'->>'publisher')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="author"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'responsible_parties'->>'author')::float;
    END IF;

    -- Spatial Reference
    IF array_length(xpath('//*[local-name()="referenceSystemInfo"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'spatial_reference'->>'crs')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="crsType"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'spatial_reference'->>'crs_type')::float;
    END IF;

    -- Data Quality
    IF array_length(xpath('//*[local-name()="lineage"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'data_quality'->>'lineage')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="conformity"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'data_quality'->>'conformity')::float;
    END IF;

    -- Constraints
    IF array_length(xpath('//*[local-name()="useConstraints"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'constraints'->>'use_constraints')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="accessConstraints"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'constraints'->>'access_constraints')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="license"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'constraints'->>'license')::float;
    END IF;

    -- Distribution
    IF array_length(xpath('//*[local-name()="distributionInfo"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'distribution'->>'url')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="format"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'distribution'->>'format')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="distributor"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'distribution'->>'distributor')::float;
    END IF;

    -- Maintenance
    IF array_length(xpath('//*[local-name()="updateFrequency"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'maintenance'->>'update_frequency')::float;
    END IF;
    IF array_length(xpath('//*[local-name()="maintenanceNotes"]', metadata_xml), 1) > 0 THEN
        score := score + (weights->'maintenance'->>'maintenance_notes')::float;
    END IF;

    RETURN (score / max_score) * 100;
END;
$$ LANGUAGE plpgsql; 