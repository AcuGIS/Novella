-- Update ISO 19115 template fields
DELETE FROM metadata_template_fields WHERE template_id = 1;

INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description, validation_rules)
VALUES
    (1, 'Lineage', 'lineage', 'textarea', true, 'Statement about the lineage of the dataset', NULL),
    (1, 'Scope', 'scope', 'select', true, 'Scope of the data quality information', '{"options": ["dataset", "series", "featureType", "feature", "attributeType", "attribute", "tile", "model", "other"]}'),
    (1, 'Conformity', 'conformity', 'select', true, 'Degree of conformity to the specification', '{"options": ["conformant", "notConformant", "notEvaluated"]}'),
    (1, 'Completeness', 'completeness', 'textarea', false, 'Completeness of the dataset', NULL),
    (1, 'Logical Consistency', 'logicalConsistency', 'textarea', false, 'Logical consistency of the dataset', NULL),
    (1, 'Positional Accuracy', 'positionalAccuracy', 'textarea', false, 'Positional accuracy of the dataset', NULL),
    (1, 'Temporal Accuracy', 'temporalAccuracy', 'textarea', false, 'Temporal accuracy of the dataset', NULL),
    (1, 'Thematic Accuracy', 'thematicAccuracy', 'textarea', false, 'Thematic accuracy of the dataset', NULL),
    (1, 'Quality Level', 'qualityLevel', 'select', false, 'Quality level of the dataset', '{"options": ["raw", "processed", "derived", "interpreted"]}');

-- Update INSPIRE template fields
DELETE FROM metadata_template_fields WHERE template_id = 2;

INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (2, 'File Identifier', 'fileIdentifier', 'string', true, 'Unique identifier for the metadata record'),
    (2, 'Title', 'title', 'string', true, 'Name of the dataset or service'),
    (2, 'Abstract', 'abstract', 'text', true, 'Brief description of the dataset'),
    (2, 'Purpose', 'purpose', 'text', true, 'Reason why the data was created'),
    (2, 'Resource Type', 'resourceType', 'string', true, 'Type of resource (dataset, series, service)'),
    (2, 'Language', 'language', 'string', true, 'Language of the metadata content'),
    (2, 'Keywords', 'keywords', 'array', false, 'Tags or themes describing the dataset'),
    (2, 'Resource URL', 'resourceUrl', 'string', true, 'URL to access the resource'),
    (2, 'INSPIRE Resource Type', 'inspireResourceType', 'string', true, 'INSPIRE resource type'),
    (2, 'INSPIRE Specification Title', 'inspireSpecificationTitle', 'string', true, 'Title of the INSPIRE specification'),
    (2, 'INSPIRE Specification Date', 'inspireSpecificationDate', 'date', true, 'Date of the INSPIRE specification'),
    (2, 'INSPIRE Specification Date Type', 'inspireSpecificationDateType', 'string', true, 'Type of the INSPIRE specification date'),
    (2, 'INSPIRE Conformity Degree', 'inspireConformityDegree', 'string', true, 'Degree of conformity to INSPIRE');

-- Update FGDC template fields
DELETE FROM metadata_template_fields WHERE template_id = 3;

INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (3, 'Origin', 'origin', 'string', true, 'Originator of the dataset'),
    (3, 'Publication Date', 'pubdate', 'date', true, 'Date of publication'),
    (3, 'Title', 'title', 'string', true, 'Name of the dataset'),
    (3, 'Geographic Form', 'geoform', 'string', true, 'Form of the geographic data'),
    (3, 'Publication Place', 'pubplace', 'string', true, 'Place of publication'),
    (3, 'Publisher', 'publish', 'string', true, 'Publisher of the dataset'),
    (3, 'Abstract', 'abstract', 'text', true, 'Brief description of the dataset'),
    (3, 'Purpose', 'purpose', 'text', true, 'Reason why the data was created'),
    (3, 'Supplemental Information', 'supplinf', 'text', false, 'Additional information about the dataset'),
    (3, 'Begin Date', 'begdate', 'date', true, 'Start date of the dataset'),
    (3, 'End Date', 'enddate', 'date', true, 'End date of the dataset'),
    (3, 'Currentness Reference', 'current', 'string', true, 'Reference to currentness of the data'),
    (3, 'Progress', 'progress', 'string', true, 'Status of the dataset'),
    (3, 'Update Frequency', 'update', 'string', true, 'Frequency of updates'),
    (3, 'West Bounding Coordinate', 'westbc', 'decimal', true, 'Westernmost coordinate'),
    (3, 'East Bounding Coordinate', 'eastbc', 'decimal', true, 'Easternmost coordinate'),
    (3, 'North Bounding Coordinate', 'northbc', 'decimal', true, 'Northernmost coordinate'),
    (3, 'South Bounding Coordinate', 'southbc', 'decimal', true, 'Southernmost coordinate');

-- Update OGC CSW template fields
DELETE FROM metadata_template_fields WHERE template_id = 4;

INSERT INTO metadata_template_fields (template_id, field_name, field_path, field_type, is_required, description)
VALUES
    (4, 'Identifier', 'identifier', 'string', true, 'Unique identifier for the record'),
    (4, 'Title', 'title', 'string', true, 'Name of the dataset or service'),
    (4, 'Type', 'type', 'string', true, 'Type of resource'),
    (4, 'Subject', 'subject', 'string', true, 'Subject of the resource'),
    (4, 'Format', 'format', 'string', true, 'Format of the resource'),
    (4, 'Date', 'date', 'date', true, 'Date of the resource'),
    (4, 'Language', 'language', 'string', true, 'Language of the resource'),
    (4, 'Rights', 'rights', 'string', true, 'Rights information'),
    (4, 'Source', 'source', 'string', true, 'Source of the resource'),
    (4, 'Creator', 'creator', 'string', true, 'Creator of the resource'),
    (4, 'Publisher', 'publisher', 'string', true, 'Publisher of the resource'),
    (4, 'Contributor', 'contributor', 'string', false, 'Contributor to the resource'),
    (4, 'Description', 'description', 'text', true, 'Description of the resource'),
    (4, 'Abstract', 'abstract', 'text', true, 'Abstract of the resource'),
    (4, 'Created', 'created', 'date', true, 'Creation date'),
    (4, 'Modified', 'modified', 'date', true, 'Last modification date'),
    (4, 'Issued', 'issued', 'date', true, 'Issue date'),
    (4, 'Lower Corner', 'lowerCorner', 'string', true, 'Lower corner of bounding box'),
    (4, 'Upper Corner', 'upperCorner', 'string', true, 'Upper corner of bounding box'),
    (4, 'Temporal Start', 'temporalStart', 'date', true, 'Start of temporal extent'),
    (4, 'Temporal End', 'temporalEnd', 'date', true, 'End of temporal extent'),
    (4, 'URI', 'uri', 'string', true, 'URI of the resource'); 