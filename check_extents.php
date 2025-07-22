<?php
require_once __DIR__ . '/vendor/autoload.php';

try {
    $db = new PDO(
        "pgsql:host=localhost;dbname=novella;user=postgres;password=_CyE6LSMJvfWH2fkKlYabhQ3f9t5oPvN",
        null,
        null,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $query = "
        SELECT 
            m.id,
            m.title,
            m.wms_layer,
            g.west_longitude,
            g.east_longitude,
            g.south_latitude,
            g.north_latitude
        FROM metadata_records m
        JOIN geographic_extents g ON g.metadata_id = m.id
        WHERE m.wms_layer IN ('Tracks', 'Fields', 'Apiary')
        ORDER BY m.wms_layer";

    $stmt = $db->query($query);
    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Found " . count($results) . " datasets:\n\n";
    foreach ($results as $row) {
        echo "Dataset: {$row['title']}\n";
        echo "WMS Layer: {$row['wms_layer']}\n";
        echo "ID: {$row['id']}\n";
        echo "Extent: {$row['west_longitude']}°W to {$row['east_longitude']}°E, ";
        echo "{$row['south_latitude']}°S to {$row['north_latitude']}°N\n\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 