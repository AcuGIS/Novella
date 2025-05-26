-- Delete all records in a safe order to maintain referential integrity
DO $$
BEGIN
    -- First, delete all datasets (this will cascade delete metadata, keywords, contacts, etc.)
    DELETE FROM datasets;

    -- Delete all OAI records (this will cascade delete record sets)
    DELETE FROM oai_records;

    -- Delete all harvest sources (this will cascade delete logs)
    DELETE FROM harvest_sources;

    -- Delete all OAI sets
    DELETE FROM oai_sets;

    -- Reset sequences
    ALTER SEQUENCE datasets_id_seq RESTART WITH 1;
    ALTER SEQUENCE oai_records_id_seq RESTART WITH 1;
    ALTER SEQUENCE harvest_sources_id_seq RESTART WITH 1;
    ALTER SEQUENCE oai_sets_id_seq RESTART WITH 1;
    ALTER SEQUENCE oai_logs_id_seq RESTART WITH 1;

    RAISE NOTICE 'All records have been deleted and sequences reset';
END $$; 