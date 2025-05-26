-- Delete orphaned datasets (datasets that are not associated with any harvest source)
DO $$
DECLARE
    orphaned_dataset RECORD;
BEGIN
    -- Find and delete datasets that are not associated with any harvest source
    FOR orphaned_dataset IN 
        SELECT d.id 
        FROM datasets d 
        LEFT JOIN oai_records r ON d.id = r.dataset_id 
        LEFT JOIN harvest_sources h ON r.harvest_source_id = h.id 
        WHERE h.id IS NULL 
        AND r.harvest_source_id IS NULL LOOP
        
        -- Delete the dataset (this will cascade delete related records in metadata, keywords, etc.)
        DELETE FROM datasets WHERE id = orphaned_dataset.id;
        
        RAISE NOTICE 'Deleted orphaned dataset with ID: %', orphaned_dataset.id;
    END LOOP;
END $$; 