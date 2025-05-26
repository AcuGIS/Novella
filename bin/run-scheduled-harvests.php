<?php

require_once __DIR__ . '/../vendor/autoload.php';

use GeoLibre\Command\RunScheduledHarvests;
use GeoLibre\Config\Database;

// Initialize database connection
$db = Database::getConnection();

// Create and run the scheduled harvests command
$command = new RunScheduledHarvests($db);
$command->run(); 