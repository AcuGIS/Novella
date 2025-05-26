-- Remove incorrect harvest source associations
UPDATE oai_records 
SET harvest_source_id = NULL 
WHERE harvest_source_id IS NOT NULL 
AND NOT EXISTS (
    SELECT 1 FROM harvest_sources h 
    WHERE h.id = oai_records.harvest_source_id
);

-- Delete orphaned datasets (those without a valid harvest source)
DELETE FROM datasets d
WHERE NOT EXISTS (
    SELECT 1 FROM oai_records r 
    JOIN harvest_sources h ON r.harvest_source_id = h.id 
    WHERE r.dataset_id = d.id
); 