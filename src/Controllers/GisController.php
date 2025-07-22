<?php

namespace Novella\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Exception;

class GisController {
    private $db;
    private $uploadDir;
    private $tempDir;

    public function __construct(PDO $db) {
        $this->db = $db;
        $this->uploadDir = dirname(__DIR__, 2) . '/storage/uploads';
        $this->tempDir = dirname(__DIR__, 2) . '/storage/temp';
        
        // Create directories if they don't exist
        foreach ([$this->uploadDir, $this->tempDir] as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0775, true);
            }
        }
    }

    // Helper function to recursively remove a directory
    private function removeDirectory($dir) {
        if (!file_exists($dir)) {
            return;
        }
        $files = array_diff(scandir($dir), array('.', '..'));
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? removeDirectory($path) : unlink($path);
        }
        rmdir($dir);
    }
    
    public function deleteByMetaId(string $id): array {
        // remove GIS Files
        $sql = 'SELECT file_path from gis_files WHERE metadata_id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $row){
            if(is_file($row['file_path'])){
                unlink($row['file_path']);
            }
        }
        
        // Delete associated GIS files first
        $stmt = $this->db->prepare("DELETE FROM gis_files WHERE metadata_id = :id");
        $stmt->execute(['id' => $id]);
        $deletedFiles = $stmt->rowCount();
        
        $qgis_proj_dir = '/var/www/data/qgis/'.$id;
        if(is_dir($qgis_proj_dir)){
            $this->removeDirectory($qgis_proj_dir);
        }
        
        return [
            'success' => true,
            'message' => "GIS files deleted successfully"
        ];
    }
    
    public function upload(Request $request, Response $response): Response {
        try {
            $uploadedFiles = $request->getUploadedFiles();
            error_log("Received uploaded files: " . print_r(array_keys($uploadedFiles), true));
            
            // Handle thumbnail upload if present
            $thumbnailPath = null;
            if (isset($uploadedFiles['thumbnail'])) {
                error_log("Thumbnail file found in upload");
                $thumbnail = $uploadedFiles['thumbnail'];
                
                if ($thumbnail->getError() !== UPLOAD_ERR_OK) {
                    $errorMessage = $this->getUploadErrorMessage($thumbnail->getError());
                    error_log("Thumbnail upload error: " . $errorMessage);
                    $response->getBody()->write(json_encode([
                        'status' => 'error',
                        'message' => 'Thumbnail upload failed: ' . $errorMessage
                    ]));
                    return $response
                        ->withHeader('Content-Type', 'application/json')
                        ->withStatus(400);
                }

                // Validate thumbnail file type
                $allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
                $thumbnailMimeType = $thumbnail->getClientMediaType();
                
                if (!in_array($thumbnailMimeType, $allowedImageTypes)) {
                    throw new Exception('Invalid thumbnail file type. Only JPEG, PNG, and GIF are allowed.');
                }

                // Create thumbnails directory if it doesn't exist
                $thumbnailDir = $this->uploadDir . '/thumbnails';
                if (!file_exists($thumbnailDir)) {
                    if (!mkdir($thumbnailDir, 0775, true)) {
                        throw new Exception('Failed to create thumbnails directory. Please check directory permissions.');
                    }
                    chmod($thumbnailDir, 0775);
                }

                // Generate unique filename for thumbnail
                $thumbnailFilename = uniqid() . '_thumb_' . $thumbnail->getClientFilename();
                $thumbnailPath = $thumbnailDir . '/' . $thumbnailFilename;

                // Move uploaded thumbnail
                try {
                    $thumbnail->moveTo($thumbnailPath);
                    if (!file_exists($thumbnailPath)) {
                        throw new Exception('Failed to save thumbnail file. Please check directory permissions.');
                    }
                    chmod($thumbnailPath, 0664);
                    
                    // Verify the file is readable
                    if (!is_readable($thumbnailPath)) {
                        throw new Exception('Uploaded thumbnail is not readable. Please check file permissions.');
                    }
                    
                    // Store relative path in database
                    $thumbnailPath = 'thumbnails/' . $thumbnailFilename;
                } catch (Exception $e) {
                    // Clean up if move failed
                    if (file_exists($thumbnailPath)) {
                        unlink($thumbnailPath);
                    }
                    throw new Exception('Failed to save thumbnail: ' . $e->getMessage());
                }
            }

            // Process GIS files if present
            $processedFiles = [];
            if (!empty($uploadedFiles['gis_files'])) {
                foreach ($uploadedFiles['gis_files'] as $file) {
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        $response->getBody()->write(json_encode([
                            'status' => 'error',
                            'message' => 'Error uploading file: ' . $this->getUploadErrorMessage($file->getError())
                        ]));
                        return $response
                            ->withHeader('Content-Type', 'application/json')
                            ->withStatus(400);
                    }

                    // Get original file type
                    $originalFileType = strtolower(pathinfo($file->getClientFilename(), PATHINFO_EXTENSION));
                    $allowedTypes = ['zip', 'shp', 'gpkg', 'tif', 'tiff', 'geotiff', 'img', 'ecw', 'jp2', 'sid', 'asc', 'grd', 'nc'];
                    
                    if (!in_array($originalFileType, $allowedTypes)) {
                        throw new Exception('Unsupported file type: ' . $originalFileType);
                    }

                    // Generate unique filename
                    $uniqueFilename = uniqid() . '_' . $file->getClientFilename();
                    $filePath = $this->uploadDir . '/' . $uniqueFilename;

                    // Move uploaded file
                    $file->moveTo($filePath);
                    chmod($filePath, 0664); // Ensure readable by web server

                    // For shapefiles, we need to extract the zip first
                    $actualFileType = $originalFileType;
                    if ($originalFileType === 'zip') {
                        $extractedPath = $this->extractShapefile($filePath);
                        if ($extractedPath) {
                            $filePath = $extractedPath;
                            $actualFileType = 'shp';
                            error_log("Extracted shapefile to: " . $filePath);
                        } else {
                            error_log("Failed to extract shapefile from: " . $filePath);
                        }
                    }

                    // Extract metadata based on file type
                    $metadata = $this->extractMetadata($filePath, $actualFileType);

                    // Store file information in database
                    $stmt = $this->db->prepare("
                        INSERT INTO gis_files (
                            file_name, file_type, file_size, file_path, mime_type,
                            thumbnail_path, coordinate_system, west_longitude, east_longitude,
                            south_latitude, north_latitude
                        ) VALUES (
                            :file_name, :file_type, :file_size, :file_path, :mime_type,
                            :thumbnail_path, :coordinate_system, :west_longitude, :east_longitude,
                            :south_latitude, :north_latitude
                        ) RETURNING id
                    ");

                    // Round spatial extent values to 6 decimal places
                    $west_longitude = isset($metadata['west_longitude']) ? round($metadata['west_longitude'], 6) : null;
                    $east_longitude = isset($metadata['east_longitude']) ? round($metadata['east_longitude'], 6) : null;
                    $south_latitude = isset($metadata['south_latitude']) ? round($metadata['south_latitude'], 6) : null;
                    $north_latitude = isset($metadata['north_latitude']) ? round($metadata['north_latitude'], 6) : null;

                    $stmt->execute([
                        'file_name' => $file->getClientFilename(),
                        'file_type' => $actualFileType,
                        'file_size' => $file->getSize(),
                        'file_path' => $filePath,
                        'mime_type' => $file->getClientMediaType(),
                        'thumbnail_path' => $thumbnailPath,
                        'coordinate_system' => $metadata['coordinate_system'] ?? null,
                        'west_longitude' => $west_longitude,
                        'east_longitude' => $east_longitude,
                        'south_latitude' => $south_latitude,
                        'north_latitude' => $north_latitude
                    ]);

                    $fileId = $stmt->fetchColumn();
                    
                    $processedFiles[] = [
                        'id' => $fileId,
                        'file_name' => $file->getClientFilename(),
                        'file_type' => $actualFileType,
                        'file_size' => $file->getSize(),
                        'thumbnail_path' => $thumbnailPath ? str_replace($this->uploadDir . '/thumbnails/', '', $thumbnailPath) : null,
                        'coordinate_system' => $metadata['coordinate_system'] ?? null,
                        'west_longitude' => $west_longitude,
                        'east_longitude' => $east_longitude,
                        'south_latitude' => $south_latitude,
                        'north_latitude' => $north_latitude
                    ];
                }
            } else if (!empty($thumbnailPath)) {
                // If only thumbnail was uploaded, create a dummy file entry
                $stmt = $this->db->prepare("
                    INSERT INTO gis_files (
                        file_name, file_type, file_size, file_path, mime_type,
                        thumbnail_path
                    ) VALUES (
                        'thumbnail_only', 'thumbnail', 0, NULL, 'image/thumbnail',
                        :thumbnail_path
                    ) RETURNING id
                ");

                $stmt->execute([
                    'thumbnail_path' => $thumbnailPath
                ]);

                $fileId = $stmt->fetchColumn();
                
                $processedFiles[] = [
                    'id' => $fileId,
                    'file_name' => 'thumbnail_only',
                    'file_type' => 'thumbnail',
                    'file_size' => 0,
                    'thumbnail_path' => $thumbnailPath
                ];
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'Files uploaded successfully',
                'files' => $processedFiles
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            error_log('Error in GIS file upload: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }
    }

    private function extractShapefile(string $zipPath): ?string {
        $tempDir = $this->tempDir . '/' . uniqid();
        mkdir($tempDir, 0775, true);
        chmod($tempDir, 0775); // Ensure directory is writable

        $zip = new \ZipArchive();
        if ($zip->open($zipPath) === TRUE) {
            $zip->extractTo($tempDir);
            $zip->close();

            // Find all shapefile components
            $shpFiles = glob($tempDir . '/*.shp');
            if (!empty($shpFiles)) {
                $shpFile = $shpFiles[0];
                // Make sure all shapefile components are readable
                $baseName = pathinfo($shpFile, PATHINFO_FILENAME);
                $extensions = ['.shp', '.shx', '.dbf', '.prj'];
                foreach ($extensions as $ext) {
                    $componentFile = $tempDir . '/' . $baseName . $ext;
                    if (file_exists($componentFile)) {
                        chmod($componentFile, 0664);
                    }
                }
                error_log("Found shapefile components in: " . $tempDir);
                return $shpFile;
            }
        }

        return null;
    }

    private function extractMetadata(string $filePath, string $fileType): array {
        $metadata = [];
        
        try {
            // Ensure we have an absolute path
            $filePath = realpath($filePath);
            if (!$filePath) {
                error_log("Could not resolve absolute path for: " . $filePath);
                return $metadata;
            }
            
            // Get the directory and filename
            $dir = dirname($filePath);
            $filename = basename($filePath);
            
            // Log the file we're processing
            error_log("Processing file: " . $filePath . " (Type: " . $fileType . ")");
            error_log("Directory: " . $dir . ", Filename: " . $filename);
            
            // Check if file exists and is readable
            if (!file_exists($filePath)) {
                error_log("File does not exist: " . $filePath);
                return $metadata;
            }
            if (!is_readable($filePath)) {
                error_log("File is not readable: " . $filePath);
                return $metadata;
            }
            
            // For shapefiles, try a different approach
            if ($fileType === 'shp') {
                // First try to read the .prj file directly
                $prjFile = pathinfo($filePath, PATHINFO_DIRNAME) . '/' . 
                          pathinfo($filePath, PATHINFO_FILENAME) . '.prj';
                if (file_exists($prjFile)) {
                    error_log("Reading PRJ file: " . $prjFile);
                    $prjContent = file_get_contents($prjFile);
                    error_log("PRJ content: " . $prjContent);
                    if (preg_match('/EPSG["\s:]+(\d+)/i', $prjContent, $matches)) {
                        $metadata['coordinate_system'] = 'EPSG:' . $matches[1];
                        error_log("Found coordinate system from PRJ: " . $metadata['coordinate_system']);
                    }
                }

                // Change to the directory containing the shapefile
                $oldDir = getcwd();
                chdir($dir);
                error_log("Changed to directory: " . $dir);

                // Try ogrinfo with JSON output first
                $ogrinfoCommand = 'ogrinfo -so -json ' . escapeshellarg($filename);
                error_log("Trying ogrinfo command: " . $ogrinfoCommand);
                $output = shell_exec($ogrinfoCommand . ' 2>&1');
                error_log("OGR output: " . ($output ?: "No output"));
                
                if ($output && !strpos($output, 'ERROR')) {
                    $info = json_decode($output, true);
                    if ($info && isset($info['layers'][0]['geometryFields'][0]['extent'])) {
                        error_log("Found extent in geometryFields");
                        $extent = $info['layers'][0]['geometryFields'][0]['extent'];
                        // The extent array is [xmin, ymin, xmax, ymax]
                        $metadata['west_longitude'] = $extent[0];
                        $metadata['south_latitude'] = $extent[1];
                        $metadata['east_longitude'] = $extent[2];
                        $metadata['north_latitude'] = $extent[3];
                        
                        // If we don't have a coordinate system yet, try to get it from the JSON
                        if (empty($metadata['coordinate_system']) && 
                            isset($info['layers'][0]['geometryFields'][0]['coordinateSystem']['wkt'])) {
                            $wkt = $info['layers'][0]['geometryFields'][0]['coordinateSystem']['wkt'];
                            if (preg_match('/EPSG["\s:]+(\d+)/i', $wkt, $matches)) {
                                $metadata['coordinate_system'] = 'EPSG:' . $matches[1];
                                error_log("Found coordinate system from OGR JSON: " . $metadata['coordinate_system']);
                            }
                        }
                        
                        error_log("Extracted extent from OGR JSON: " . json_encode($metadata));
                    }
                }

                // If we still don't have extent, try non-JSON ogrinfo
                if (empty($metadata['west_longitude'])) {
                    $ogrinfoCommand = 'ogrinfo -so ' . escapeshellarg($filename);
                    error_log("Trying ogrinfo command: " . $ogrinfoCommand);
                    $output = shell_exec($ogrinfoCommand . ' 2>&1');
                    error_log("OGR output: " . ($output ?: "No output"));
                    
                    if ($output && !strpos($output, 'ERROR')) {
                        // Parse non-JSON output
                        if (preg_match('/Extent:\s*\(([^,]+),\s*([^)]+)\)\s*-\s*\(([^,]+),\s*([^)]+)\)/', $output, $matches)) {
                            error_log("Found OGR extent from text output");
                            $metadata['west_longitude'] = floatval($matches[1]);
                            $metadata['south_latitude'] = floatval($matches[2]);
                            $metadata['east_longitude'] = floatval($matches[3]);
                            $metadata['north_latitude'] = floatval($matches[4]);
                        }
                    }
                }

                // Change back to the original directory
                chdir($oldDir);
                error_log("Changed back to directory: " . $oldDir);

            } else {
                // For raster files, use gdalinfo
                $command = 'gdalinfo -json ' . escapeshellarg($filePath);
                error_log("Executing gdalinfo command: " . $command);
                
                $output = shell_exec($command . ' 2>&1'); // Capture stderr too
                error_log("GDAL output: " . ($output ?: "No output"));
                
                if ($output && !strpos($output, 'ERROR')) {
                    $info = json_decode($output, true);
                    if ($info) {
                        error_log("Decoded JSON info: " . json_encode(array_keys($info)));
                        
                        // Extract coordinate system
                        if (isset($info['coordinateSystem']['wkt'])) {
                            error_log("Found coordinate system: " . $info['coordinateSystem']['wkt']);
                            if (preg_match('/EPSG["\s:]+(\d+)/i', $info['coordinateSystem']['wkt'], $matches)) {
                                $metadata['coordinate_system'] = 'EPSG:' . $matches[1];
                                error_log("Found coordinate system: " . $metadata['coordinate_system']);
                            }
                        }

                        // Try to get extent from geoTransform first
                        if (isset($info['geoTransform'])) {
                            error_log("Found geoTransform: " . json_encode($info['geoTransform']));
                            $transform = $info['geoTransform'];
                            $width = $info['size'][0];
                            $height = $info['size'][1];
                            
                            // Calculate the four corners
                            $corners = [
                                [$transform[0], $transform[3]], // Upper left
                                [$transform[0] + $width * $transform[1], $transform[3]], // Upper right
                                [$transform[0], $transform[3] + $height * $transform[5]], // Lower left
                                [$transform[0] + $width * $transform[1], $transform[3] + $height * $transform[5]] // Lower right
                            ];
                            
                            error_log("Calculated corners: " . json_encode($corners));
                            
                            // Find min/max coordinates
                            $xCoords = array_column($corners, 0);
                            $yCoords = array_column($corners, 1);
                            
                            $metadata['west_longitude'] = min($xCoords);
                            $metadata['east_longitude'] = max($xCoords);
                            $metadata['south_latitude'] = min($yCoords);
                            $metadata['north_latitude'] = max($yCoords);
                            
                            error_log("Initial extent from geoTransform: " . json_encode($metadata));
                            
                            // If we have a coordinate system, transform the coordinates
                            if (!empty($metadata['coordinate_system']) && $metadata['coordinate_system'] !== 'EPSG:4326') {
                                $transformCommand = sprintf(
                                    'gdaltransform -s_srs "%s" -t_srs EPSG:4326 <<EOF
                                    %f %f
                                    %f %f
EOF',
                                    $metadata['coordinate_system'],
                                    $metadata['west_longitude'], $metadata['south_latitude'],
                                    $metadata['east_longitude'], $metadata['north_latitude']
                                );
                                error_log("Executing transform command: " . $transformCommand);
                                
                                $transformed = shell_exec($transformCommand . ' 2>&1');
                                error_log("Transform output: " . ($transformed ?: "No output"));
                                
                                if ($transformed) {
                                    $coords = array_map('floatval', preg_split('/\s+/', trim($transformed)));
                                    if (count($coords) >= 4) {
                                        $metadata['west_longitude'] = min($coords[0], $coords[2]);
                                        $metadata['east_longitude'] = max($coords[0], $coords[2]);
                                        $metadata['south_latitude'] = min($coords[1], $coords[3]);
                                        $metadata['north_latitude'] = max($coords[1], $coords[3]);
                                        error_log("Transformed extent: " . json_encode($metadata));
                                    }
                                }
                            }
                        }
                        // If we don't have extent from geoTransform, try cornerCoordinates
                        elseif (isset($info['cornerCoordinates'])) {
                            error_log("Found corner coordinates: " . json_encode($info['cornerCoordinates']));
                            $corners = $info['cornerCoordinates'];
                            if (isset($corners['lowerLeft']) && isset($corners['upperRight'])) {
                                $metadata['west_longitude'] = min($corners['lowerLeft'][0], $corners['upperRight'][0]);
                                $metadata['east_longitude'] = max($corners['lowerLeft'][0], $corners['upperRight'][0]);
                                $metadata['south_latitude'] = min($corners['lowerLeft'][1], $corners['upperRight'][1]);
                                $metadata['north_latitude'] = max($corners['lowerLeft'][1], $corners['upperRight'][1]);
                                error_log("Extracted extent from corner coordinates: " . json_encode($metadata));
                            }
                        }
                        // Finally, try wgs84Extent
                        elseif (isset($info['wgs84Extent'])) {
                            error_log("Found WGS84 extent: " . json_encode($info['wgs84Extent']));
                            $extent = $info['wgs84Extent'];
                            if (isset($extent['westBoundLongitude'])) {
                                $metadata['west_longitude'] = $extent['westBoundLongitude'];
                                $metadata['east_longitude'] = $extent['eastBoundLongitude'];
                                $metadata['south_latitude'] = $extent['southBoundLatitude'];
                                $metadata['north_latitude'] = $extent['northBoundLatitude'];
                                error_log("Extracted extent from WGS84 extent: " . json_encode($metadata));
                            }
                        }
                        
                        // If we still don't have extent, try getting it from the bounds
                        if (empty($metadata['west_longitude']) && isset($info['bounds'])) {
                            error_log("Found bounds: " . json_encode($info['bounds']));
                            $bounds = $info['bounds'];
                            if (isset($bounds['coordinates'][0])) {
                                $coords = $bounds['coordinates'][0];
                                $xCoords = array_column($coords, 0);
                                $yCoords = array_column($coords, 1);
                                
                                $metadata['west_longitude'] = min($xCoords);
                                $metadata['east_longitude'] = max($xCoords);
                                $metadata['south_latitude'] = min($yCoords);
                                $metadata['north_latitude'] = max($yCoords);
                                error_log("Extracted extent from bounds: " . json_encode($metadata));
                            }
                        }
                    }
                }
            }

        } catch (Exception $e) {
            error_log('Error extracting metadata: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
        }

        error_log("Final metadata: " . json_encode($metadata));
        return $metadata;
    }

    private function getUploadErrorMessage(int $errorCode): string {
        switch ($errorCode) {
            case UPLOAD_ERR_INI_SIZE:
                return 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            case UPLOAD_ERR_FORM_SIZE:
                return 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
            case UPLOAD_ERR_PARTIAL:
                return 'The uploaded file was only partially uploaded';
            case UPLOAD_ERR_NO_FILE:
                return 'No file was uploaded';
            case UPLOAD_ERR_NO_TMP_DIR:
                return 'Missing a temporary folder';
            case UPLOAD_ERR_CANT_WRITE:
                return 'Failed to write file to disk';
            case UPLOAD_ERR_EXTENSION:
                return 'A PHP extension stopped the file upload';
            default:
                return 'Unknown upload error';
        }
    }
}
