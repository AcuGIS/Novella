<?php

use DI\Container;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\PhpRenderer;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Novella\Models\Metadata;
use Novella\Controllers\WmsController;
use Novella\Controllers\GisController;
use Novella\Controllers\TopicsController;
use Novella\Controllers\KeywordsController;
use Novella\Controllers\HarvestController;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Novella\Models\HarvestSettings;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Novella\Controllers\UsersController;
use Throwable as GlobalThrowable;
use TCPDF as GlobalTCPDF;
use GuzzleHttp\Psr7\LazyOpenStream;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Enable error reporting in development
if ($_ENV['APP_ENV'] === 'development' || $_ENV['APP_DEBUG'] === 'true') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Create Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions(__DIR__ . '/../src/container.php');
$container = $containerBuilder->build();

// Create App
AppFactory::setContainer($container);
$app = AppFactory::create();

// Register database connection
$container = $app->getContainer();
$container->set('db', function ($c) {
    $settings = require __DIR__ . '/../config/database.php';
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
        $settings['host'],
        $settings['port'],
        $settings['database'],
        $settings['username'],
        $settings['password']
    );
    
    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $pdo;
    } catch (PDOException $e) {
        error_log('Database connection error: ' . $e->getMessage());
        throw new Exception('Database connection failed');
    }
});

// Add Error Middleware
$errorMiddleware = $app->addErrorMiddleware(true, true, true);

// Configure error handler to return JSON for API routes
$errorHandler = $errorMiddleware->getDefaultErrorHandler();
$errorHandler->forceContentType('application/json');

// Add custom error handler for JSON responses
$errorMiddleware->setErrorHandler(
    GlobalThrowable::class,
    function (Request $request, GlobalThrowable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails) use ($app) {
        $response = $app->getResponseFactory()->createResponse();
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $exception->getMessage(),
            'trace' => $_ENV['APP_ENV'] === 'development' ? $exception->getTraceAsString() : null
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
);

// Add routing middleware
$app->addRoutingMiddleware();

// Add body parsing middleware
$app->addBodyParsingMiddleware();

// Add Twig middleware
$twig = $container->get(Twig::class);
$app->add(TwigMiddleware::create($app, $twig));

// Create authentication middleware
class AuthMiddleware implements MiddlewareInterface
{
    private $container;
    private $publicRoutes = ['/', '/viewer', '/login', '/datasets', '/wms/capabilities', '/api/datasets', '/api/datasets/search-by-bbox', '/api/datasets/by-ids', '/about'];

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $path = $request->getUri()->getPath();
        
        // Allow public access to XML and PDF export routes
        if (
            in_array($path, $this->publicRoutes) ||
            strpos($path, '/datasets/') === 0 ||
            preg_match('#^/metadata/[^/]+/(xml|pdf)$#', $path)
        ) {
            return $handler->handle($request);
        }

        $auth = $this->container->get('auth');
        if (!$auth->isLoggedIn()) {
            $response = new \Slim\Psr7\Response();
            return $response->withHeader('Location', '/login')->withStatus(302);
        }
        return $handler->handle($request);
    }
}

// Add middleware to the app
$app->add(new AuthMiddleware($container));

// Define routes directly on the app instance
// Public routes
$app->get('/', function (Request $request, Response $response) {
    $metadata = $this->get(Metadata::class);
    
    // Get search and filter parameters from query string
    $searchTerm = $request->getQueryParams()['search'] ?? '';
    $topicId = $request->getQueryParams()['topic'] ?? null;
    $keyword = $request->getQueryParams()['keyword'] ?? null;
    $dateFrom = $request->getQueryParams()['date_from'] ?? null;
    $dateTo = $request->getQueryParams()['date_to'] ?? null;
    
    // Get pagination parameters from query string
    $page = max(1, intval($request->getQueryParams()['page'] ?? 1));
    $perPage = max(1, min(50, intval($request->getQueryParams()['per_page'] ?? 12))); // Limit max items per page to 50
    
    // If we have any search/filter parameters, use the search method
    if (!empty($searchTerm) || !empty($topicId) || !empty($keyword) || !empty($dateFrom) || !empty($dateTo)) {
        $result = $metadata->search($searchTerm, $topicId, $keyword, $dateFrom, $dateTo, $page, $perPage);
    } else {
        $result = $metadata->getAll($page, $perPage);
    }
    
    $datasets = $result['datasets'];
    $pagination = $result['pagination'];
    
    // Get topics and keywords for the search sidebar
    $topicsController = new TopicsController($this->get(PDO::class));
    $keywordsController = new KeywordsController($this->get(PDO::class));
    $topics = $topicsController->index()['topics'];
    $keywords = $keywordsController->index()['keywords'];
    
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'datasets.twig', [
        'datasets' => $datasets,
        'topics' => $topics,
        'keywords' => $keywords,
        'pagination' => $pagination,
        'search_term' => $searchTerm,
        'selected_topic' => $topicId,
        'selected_keyword' => $keyword,
        'date_from' => $dateFrom,
        'date_to' => $dateTo
    ]);
});

$app->get('/about', function (Request $request, Response $response) {
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'about.twig');
});

$app->get('/viewer', function (Request $request, Response $response) {
    $metadata = $this->get(Metadata::class);
    $result = $metadata->getAll(1, 1000); // Get all datasets for viewer, with a high limit
    $datasets = $result['datasets'];
    
    // Transform datasets for the viewer, including WMS info
    $viewerDatasets = array_map(function($dataset) use ($metadata) {
        $fullDataset = $metadata->getById($dataset['id']);
        $viewerDataset = [
            'id' => $dataset['id'],
            'title' => $dataset['title'],
            'wmsUrl' => $fullDataset['wms_url'] ?? '',
            'wmsLayer' => $fullDataset['wms_layer'] ?? ''
        ];
        return $viewerDataset;
    }, $datasets);
    
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'viewer.twig', ['datasets' => $viewerDatasets]);
});

// Login routes
$app->get('/login', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if ($auth->isLoggedIn()) {
        return $response->withHeader('Location', '/')->withStatus(302);
    }
    return $this->get(Twig::class)->render($response, 'login.twig');
});

$app->post('/login', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    $data = $request->getParsedBody();
    $username = $data['username'] ?? '';
    $password = $data['password'] ?? '';

    if (empty($username) || empty($password)) {
        return $this->get(Twig::class)->render($response, 'login.twig', [
            'error' => 'Please enter both username and password'
        ]);
    }

    if ($auth->login($username, $password)) {
        return $response->withHeader('Location', '/')->withStatus(302);
    }

    return $this->get(Twig::class)->render($response, 'login.twig', [
        'error' => 'Invalid username or password'
    ]);
});

// Protected routes
$app->get('/logout', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    $auth->logout();
    return $response->withHeader('Location', '/login')->withStatus(302);
});

$app->get('/form[/{id}]', function (Request $request, Response $response, array $args) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('publish_dataset')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    // Get topics and keywords data
    $topicsController = new TopicsController($this->get(PDO::class));
    $keywordsController = new KeywordsController($this->get(PDO::class));
    
    $topics = $topicsController->index()['topics'];
    $keywords = $keywordsController->index()['keywords'];
    
    $twig = $this->get(Twig::class);
    
    // If ID is provided, get the dataset for editing
    $dataset = null;
    if (isset($args['id'])) {
        $metadata = $this->get(Metadata::class);
        $dataset = $metadata->getById($args['id']);
        if (!$dataset) {
            return $twig->render($response, 'error.twig', ['message' => 'Dataset not found.']);
        }
    }
    
    return $twig->render($response, 'form.twig', [
        'topics' => $topics,
        'keywords' => $keywords,
        'dataset' => $dataset,
        'is_edit' => isset($args['id']),
        'container_class' => 'container mx-auto'
    ]);
});

$app->get('/datasets/{id}', function (Request $request, Response $response, array $args) {
    $metadata = $this->get(Metadata::class);
    $dataset = $metadata->getById($args['id']);
    $twig = $this->get(Twig::class);
    if (!$dataset) {
        return $twig->render($response, 'error.twig', ['message' => 'Dataset not found.']);
    }
    return $twig->render($response, 'dataset_detail.twig', ['dataset' => $dataset]);
});


$app->get('/datasets/{meta_id}/{file_id}', function (Request $request, Response $response, array $args) {
    
    $db = $this->get(PDO::class);
    $stmt = $db->prepare("SELECT file_name, file_path FROM gis_files WHERE id = :file_id AND metadata_id = :meta_id");
    $stmt->execute(['file_id' => $args['file_id'], 'meta_id' => $args['meta_id'] ]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$result) {
        return $twig->render($response, 'error.twig', ['message' => 'Dataset file not found.']);
    }
    
    if(!str_starts_with($result['file_path'],'/')){
        $result['file_path'] = '/var/www/novella/storage/uploads/'.$result['file_path'];
    }
    
    // Set response headers for XML display in browser
    $response = $response->withHeader('Content-Type', mime_content_type($result['file_path']));
    $response = $response->withHeader('Content-Size', filesize($result['file_path']));
    $response = $response->withHeader('Content-disposition', 'attachment; filename="'.$result['file_name'].'"');

    // Remove the Content-Disposition header to display in browser instead of downloading
    
    try{
        // Output the file
        $fStream = new LazyOpenStream($result['file_path'], 'r');
        return $response->withBody($fStream);
    
    } catch (Exception $e) {
        error_log('Error in /datasets/{id}/{file_id} endpoint: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

$app->map(['DELETE', 'POST'], '/datasets/{id}/delete', function (Request $request, Response $response, array $args) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('edit_dataset')) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    try {
        $db = $this->get(PDO::class);
        $metadata = $this->get(Metadata::class);
        $gisController = new \Novella\Controllers\GisController($db);
        
        $gisController->deleteByMetaId($args['id']);
        $result = $metadata->delete($args['id']);
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error deleting dataset: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

$app->post('/metadata', function (Request $request, Response $response) {
    try {
        // Get both parsed body and uploaded files
        $parsedBody = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        error_log("Received metadata: " . print_r($parsedBody, true));
        error_log("Received files: " . print_r(array_keys($uploadedFiles), true));

        // Validate required fields
        $requiredFields = ['title', 'abstract', 'west_longitude', 'east_longitude', 'south_latitude', 'north_latitude'];
        $missingFields = array_filter($requiredFields, function($field) use ($parsedBody) {
            return empty($parsedBody[$field]);
        });

        if (!empty($missingFields)) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Missing required fields: ' . implode(', ', $missingFields)
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // Create metadata record
        $metadataModel = new Metadata($this->get(PDO::class), $this);
        $metadataResult = $metadataModel->create($parsedBody);
        
        if (!$metadataResult['success']) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $metadataResult['message']
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        $result = [
            'status' => 'success',
            'message' => $metadataResult['message'],
            'id' => $metadataResult['id'],
            'files' => []
        ];

        // Handle file uploads if present
        if (!empty($uploadedFiles)) {
            error_log("Processing file uploads...");
            
            // Handle thumbnail upload if present
            if (isset($uploadedFiles['thumbnail']) && $uploadedFiles['thumbnail']->getError() !== UPLOAD_ERR_NO_FILE) {
                $thumbnail = $uploadedFiles['thumbnail'];
                
                if ($thumbnail->getError() !== UPLOAD_ERR_OK) {
                    throw new Exception('Thumbnail upload failed: ' . getUploadErrorMessage($thumbnail->getError()));
                }

                // Create uploads directory if it doesn't exist
                $uploadDir = dirname(__DIR__) . '/storage/uploads/thumbnails';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $extension = pathinfo($thumbnail->getClientFilename(), PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $filepath = $uploadDir . '/' . $filename;

                // Move uploaded file
                $thumbnail->moveTo($filepath);

                // Insert thumbnail info into gis_files table
                $db = $this->get(PDO::class);
                $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type, thumbnail_path) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type, :thumbnail_path)");
                $stmt->execute([
                    'metadata_id' => $metadataResult['id'],
                    'file_name' => $thumbnail->getClientFilename(),
                    'file_type' => 'thumbnail',
                    'file_size' => $thumbnail->getSize(),
                    'file_path' => $filename,
                    'mime_type' => $thumbnail->getClientMediaType(),
                    'thumbnail_path' => $filename
                ]);

                $result['files'][] = [
                    'file_name' => $thumbnail->getClientFilename(),
                    'file_type' => 'thumbnail',
                    'file_size' => $thumbnail->getSize(),
                    'file_path' => $filename
                ];
            }

            // Handle GIS files if present
            if (isset($uploadedFiles['gis_files'])) {
                foreach ($uploadedFiles['gis_files'] as $file) {
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        error_log('Error with GIS file: ' . getUploadErrorMessage($file->getError()));
                        continue;
                    }
                    $uploadDir = dirname(__DIR__) . '/storage/uploads/';
                    $filepath = $uploadDir . '/'. uniqid() .'_'. $file->getClientFilename();
    
                    // Move uploaded file
                    $file->moveTo($filepath);

                    // Insert file info into gis_files table
                    $db = $this->get(PDO::class);
                    $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type)");
                    $stmt->execute([
                        'metadata_id' => $metadataResult['id'],
                        'file_name' => $file->getClientFilename(),
                        'file_type' => pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
                        'file_size' => $file->getSize(),
                        'file_path' => $filepath,
                        'mime_type' => $file->getClientMediaType()
                    ]);

                    $result['files'][] = [
                        'file_name' => $file->getClientFilename(),
                        'file_type' => pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
                        'file_size' => $file->getSize(),
                        'file_path' => $file->getClientFilename()
                    ];
                }
            }
        }

        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error in metadata creation: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// Helper function for upload error messages
function getUploadErrorMessage($errorCode) {
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

$app->get('/metadata/{id}/xml', function (Request $request, Response $response, array $args) {
    try {
        $metadata = $this->get(Metadata::class);
        $data = $metadata->getById($args['id']);
        
        if (!$data) {
            throw new Exception('Metadata record not found');
        }

        // Create XML document
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><gmd:MD_Metadata xmlns:gmd="http://www.isotc211.org/2005/gmd" xmlns:gco="http://www.isotc211.org/2005/gco" xmlns:gml="http://www.opengis.net/gml" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.isotc211.org/2005/gmd http://schemas.opengis.net/iso/19139/20060504/gmd/gmd.xsd"></gmd:MD_Metadata>');

        // Basic metadata
        $xml->addChild('gmd:fileIdentifier', $data['id'], 'gco');
        
        $language = $xml->addChild('gmd:language');
        $language->addChild('gmd:LanguageCode', $data['metadata_language'] ?? 'eng', 'gco');
        
        if ($data['character_set']) {
            $charSet = $xml->addChild('gmd:characterSet');
            $charSet->addChild('gmd:MD_CharacterSetCode', $data['character_set'], 'gco');
        }
        
        $hierarchyLevel = $xml->addChild('gmd:hierarchyLevel');
        $hierarchyLevel->addChild('gmd:MD_ScopeCode', 'dataset', 'gco');

        // Add metadata date
        if ($data['metadata_date']) {
            $dateStamp = $xml->addChild('gmd:dateStamp');
            $dateStamp->addChild('gco:DateTime', $data['metadata_date']);
        }

        // Add metadata standard
        $metadataStandardName = $xml->addChild('gmd:metadataStandardName');
        $metadataStandardName->addChild('gco:CharacterString', 'ISO 19115 / INSPIRE');
        
        // Contact information
        if ($data['point_of_contact_org'] || $data['contact_org'] || $data['metadata_point_of_contact'] || $data['metadata_poc_organization'] || $data['metadata_poc_email']) {
            $contact = $xml->addChild('gmd:contact');
            $CI_ResponsibleParty = $contact->addChild('gmd:CI_ResponsibleParty');
            
            // Organization name (prioritize metadata POC organization if available)
            if ($data['metadata_poc_organization']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['metadata_poc_organization']);
            } elseif ($data['point_of_contact_org']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['point_of_contact_org']);
            } elseif ($data['contact_org']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['contact_org']);
            }
            
            // Individual name
            if ($data['metadata_point_of_contact']) {
                $individualName = $CI_ResponsibleParty->addChild('gmd:individualName');
                $individualName->addChild('gco:CharacterString', $data['metadata_point_of_contact']);
            }
            
            // Contact info
            if ($data['metadata_poc_email']) {
                $contactInfo = $CI_ResponsibleParty->addChild('gmd:contactInfo');
                $CI_Contact = $contactInfo->addChild('gmd:CI_Contact');
                $address = $CI_Contact->addChild('gmd:address');
                $CI_Address = $address->addChild('gmd:CI_Address');
                $electronicMailAddress = $CI_Address->addChild('gmd:electronicMailAddress');
                $electronicMailAddress->addChild('gco:CharacterString', $data['metadata_poc_email']);
            }
            
            // Role
            $role = $CI_ResponsibleParty->addChild('gmd:role');
            if ($data['metadata_poc_role']) {
                $role->addChild('gmd:CI_RoleCode', $data['metadata_poc_role'], 'gco');
            } else {
                $role->addChild('gmd:CI_RoleCode', 'pointOfContact', 'gco');
            }
        }

        // Identification information
        $identificationInfo = $xml->addChild('gmd:identificationInfo');
        $MD_DataIdentification = $identificationInfo->addChild('gmd:MD_DataIdentification');
        
        // Citation
        $citation = $MD_DataIdentification->addChild('gmd:citation');
        $CI_Citation = $citation->addChild('gmd:CI_Citation');
        
        $title = $CI_Citation->addChild('gmd:title');
        $title->addChild('gco:CharacterString', $data['title']);
        
        if ($data['citation_date']) {
            $date = $CI_Citation->addChild('gmd:date');
            $CI_Date = $date->addChild('gmd:CI_Date');
            $dateValue = $CI_Date->addChild('gmd:date');
            $dateValue->addChild('gco:Date', $data['citation_date']);
            $dateType = $CI_Date->addChild('gmd:dateType');
            $dateType->addChild('gmd:CI_DateTypeCode', 'creation', 'gco');
        }

        // Responsible party for citation
        if ($data['responsible_org'] || $data['responsible_person'] || $data['role']) {
            $citedResponsibleParty = $CI_Citation->addChild('gmd:citedResponsibleParty');
            $CI_ResponsibleParty = $citedResponsibleParty->addChild('gmd:CI_ResponsibleParty');
            
            if ($data['responsible_org']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['responsible_org']);
            }
            
            if ($data['responsible_person']) {
                $individualName = $CI_ResponsibleParty->addChild('gmd:individualName');
                $individualName->addChild('gco:CharacterString', $data['responsible_person']);
            }
            
            if ($data['role']) {
                $role = $CI_ResponsibleParty->addChild('gmd:role');
                $role->addChild('gmd:CI_RoleCode', $data['role'], 'gco');
            }
        }

        // Add responsible parties section
        if ($data['responsible_org'] || $data['responsible_person'] || $data['role']) {
            $responsibleParties = $MD_DataIdentification->addChild('gmd:responsibleParties');
            $CI_ResponsibleParty = $responsibleParties->addChild('gmd:CI_ResponsibleParty');
            
            if ($data['responsible_org']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['responsible_org']);
            }
            
            if ($data['responsible_person']) {
                $individualName = $CI_ResponsibleParty->addChild('gmd:individualName');
                $individualName->addChild('gco:CharacterString', $data['responsible_person']);
            }
            
            if ($data['role']) {
                $role = $CI_ResponsibleParty->addChild('gmd:role');
                $role->addChild('gmd:CI_RoleCode', $data['role'], 'gco');
            }
        }

        // Add metadata responsible party
        if ($data['metadata_poc_organization'] || $data['metadata_point_of_contact'] || $data['metadata_poc_email'] || $data['metadata_poc_role']) {
            $metadataResponsibleParty = $xml->addChild('gmd:metadataResponsibleParty');
            $CI_ResponsibleParty = $metadataResponsibleParty->addChild('gmd:CI_ResponsibleParty');
            
            if ($data['metadata_poc_organization']) {
                $orgName = $CI_ResponsibleParty->addChild('gmd:organisationName');
                $orgName->addChild('gco:CharacterString', $data['metadata_poc_organization']);
            }
            
            if ($data['metadata_point_of_contact']) {
                $individualName = $CI_ResponsibleParty->addChild('gmd:individualName');
                $individualName->addChild('gco:CharacterString', $data['metadata_point_of_contact']);
            }
            
            if ($data['metadata_poc_email']) {
                $contactInfo = $CI_ResponsibleParty->addChild('gmd:contactInfo');
                $CI_Contact = $contactInfo->addChild('gmd:CI_Contact');
                $address = $CI_Contact->addChild('gmd:address');
                $CI_Address = $address->addChild('gmd:CI_Address');
                $electronicMailAddress = $CI_Address->addChild('gmd:electronicMailAddress');
                $electronicMailAddress->addChild('gco:CharacterString', $data['metadata_poc_email']);
            }
            
            if ($data['metadata_poc_role']) {
                $role = $CI_ResponsibleParty->addChild('gmd:role');
                $role->addChild('gmd:CI_RoleCode', $data['metadata_poc_role'], 'gco');
            } else {
                $role = $CI_ResponsibleParty->addChild('gmd:role');
                $role->addChild('gmd:CI_RoleCode', 'pointOfContact', 'gco');
            }
        }

        // Abstract and purpose
        if ($data['abstract']) {
            $abstract = $MD_DataIdentification->addChild('gmd:abstract');
            $abstract->addChild('gco:CharacterString', $data['abstract']);
        }
        
        if ($data['purpose']) {
            $purpose = $MD_DataIdentification->addChild('gmd:purpose');
            $purpose->addChild('gco:CharacterString', $data['purpose']);
        }

        // Keywords
        if ($data['keywords']) {
            $descriptiveKeywords = $MD_DataIdentification->addChild('gmd:descriptiveKeywords');
            $MD_Keywords = $descriptiveKeywords->addChild('gmd:MD_Keywords');
            
            if (is_array($data['keywords'])) {
                foreach ($data['keywords'] as $keyword) {
                    $keywordElement = $MD_Keywords->addChild('gmd:keyword');
                    $keywordElement->addChild('gco:CharacterString', $keyword);
                }
            } else {
                $keywordElement = $MD_Keywords->addChild('gmd:keyword');
                $keywordElement->addChild('gco:CharacterString', $data['keywords']);
            }
        }

        // Topic and INSPIRE theme
        if ($data['topic_name']) {
            $topicCategory = $MD_DataIdentification->addChild('gmd:topicCategory');
            $topicCategory->addChild('gmd:MD_TopicCategoryCode', $data['topic_name']);
        }
        
        if ($data['inspire_theme_name']) {
            $descriptiveKeywords = $MD_DataIdentification->addChild('gmd:descriptiveKeywords');
            $MD_Keywords = $descriptiveKeywords->addChild('gmd:MD_Keywords');
            $keywordElement = $MD_Keywords->addChild('gmd:keyword');
            $keywordElement->addChild('gco:CharacterString', $data['inspire_theme_name']);
            $thesaurusName = $MD_Keywords->addChild('gmd:thesaurusName');
            $CI_Citation = $thesaurusName->addChild('gmd:CI_Citation');
            $title = $CI_Citation->addChild('gmd:title');
            $title->addChild('gco:CharacterString', 'INSPIRE Theme');
        }

        // Resource constraints
        if ($data['resource_type']) {
            $resourceConstraints = $MD_DataIdentification->addChild('gmd:resourceConstraints');
            $MD_LegalConstraints = $resourceConstraints->addChild('gmd:MD_LegalConstraints');
            $useLimitation = $MD_LegalConstraints->addChild('gmd:useLimitation');
            $useLimitation->addChild('gco:CharacterString', $data['resource_type']);
        }

        // Spatial representation type
        $spatialRepresentationType = $MD_DataIdentification->addChild('gmd:spatialRepresentationType');
        $spatialRepresentationType->addChild('gmd:MD_SpatialRepresentationTypeCode', 'vector', 'gco');

        // Spatial extent
        if ($data['west_longitude'] && $data['east_longitude'] && $data['south_latitude'] && $data['north_latitude']) {
            $extent = $MD_DataIdentification->addChild('gmd:extent');
            $EX_Extent = $extent->addChild('gmd:EX_Extent');
            $geographicElement = $EX_Extent->addChild('gmd:geographicElement');
            $EX_GeographicBoundingBox = $geographicElement->addChild('gmd:EX_GeographicBoundingBox');
            
            $westBoundLongitude = $EX_GeographicBoundingBox->addChild('gmd:westBoundLongitude');
            $westBoundLongitude->addChild('gco:Decimal', $data['west_longitude']);
            
            $eastBoundLongitude = $EX_GeographicBoundingBox->addChild('gmd:eastBoundLongitude');
            $eastBoundLongitude->addChild('gco:Decimal', $data['east_longitude']);
            
            $southBoundLatitude = $EX_GeographicBoundingBox->addChild('gmd:southBoundLatitude');
            $southBoundLatitude->addChild('gco:Decimal', $data['south_latitude']);
            
            $northBoundLatitude = $EX_GeographicBoundingBox->addChild('gmd:northBoundLatitude');
            $northBoundLatitude->addChild('gco:Decimal', $data['north_latitude']);
        }

        // Temporal extent
        if ($data['start_date'] || $data['end_date']) {
            $extent = $MD_DataIdentification->addChild('gmd:extent');
            $EX_Extent = $extent->addChild('gmd:EX_Extent');
            $temporalElement = $EX_Extent->addChild('gmd:temporalElement');
            $EX_TemporalExtent = $temporalElement->addChild('gmd:EX_TemporalExtent');
            $extent = $EX_TemporalExtent->addChild('gmd:extent');
            
            if ($data['start_date'] || $data['end_date']) {
                $TimePeriod = $extent->addChild('gml:TimePeriod');
                $TimePeriod->addAttribute('gml:id', 'TP1');
                
                if ($data['start_date']) {
                    $beginPosition = $TimePeriod->addChild('gml:beginPosition', $data['start_date']);
                }
                
                if ($data['end_date']) {
                    $endPosition = $TimePeriod->addChild('gml:endPosition', $data['end_date']);
                }
            }
        }

        // Coordinate reference system
        if ($data['coordinate_system']) {
            $referenceSystemInfo = $xml->addChild('gmd:referenceSystemInfo');
            $MD_ReferenceSystem = $referenceSystemInfo->addChild('gmd:MD_ReferenceSystem');
            $referenceSystemIdentifier = $MD_ReferenceSystem->addChild('gmd:referenceSystemIdentifier');
            $RS_Identifier = $referenceSystemIdentifier->addChild('gmd:RS_Identifier');
            $code = $RS_Identifier->addChild('gmd:code');
            $code->addChild('gco:CharacterString', $data['coordinate_system']);
        }

        // Spatial resolution
        if ($data['spatial_resolution']) {
            $spatialResolution = $MD_DataIdentification->addChild('gmd:spatialResolution');
            $MD_Resolution = $spatialResolution->addChild('gmd:MD_Resolution');
            $equivalentScale = $MD_Resolution->addChild('gmd:equivalentScale');
            $MD_RepresentativeFraction = $equivalentScale->addChild('gmd:MD_RepresentativeFraction');
            $denominator = $MD_RepresentativeFraction->addChild('gmd:denominator');
            $denominator->addChild('gco:Integer', $data['spatial_resolution']);
        }

        // Data quality
        if ($data['lineage']) {
            $dataQualityInfo = $xml->addChild('gmd:dataQualityInfo');
            $DQ_DataQuality = $dataQualityInfo->addChild('gmd:DQ_DataQuality');
            $scope = $DQ_DataQuality->addChild('gmd:scope');
            $DQ_Scope = $scope->addChild('gmd:DQ_Scope');
            $level = $DQ_Scope->addChild('gmd:level');
            $level->addChild('gmd:MD_ScopeCode', 'dataset', 'gco');
            
            $lineage = $DQ_DataQuality->addChild('gmd:lineage');
            $LI_Lineage = $lineage->addChild('gmd:LI_Lineage');
            $statement = $LI_Lineage->addChild('gmd:statement');
            $statement->addChild('gco:CharacterString', $data['lineage']);
        }

        // Distribution information
        if ($data['distribution_url'] || $data['data_format'] || $data['service_url'] || $data['coupled_resource']) {
            $distributionInfo = $xml->addChild('gmd:distributionInfo');
            $MD_Distribution = $distributionInfo->addChild('gmd:MD_Distribution');
            
            if ($data['distribution_url']) {
                $transferOptions = $MD_Distribution->addChild('gmd:transferOptions');
                $MD_DigitalTransferOptions = $transferOptions->addChild('gmd:MD_DigitalTransferOptions');
                $onLine = $MD_DigitalTransferOptions->addChild('gmd:onLine');
                $CI_OnlineResource = $onLine->addChild('gmd:CI_OnlineResource');
                $linkage = $CI_OnlineResource->addChild('gmd:linkage');
                $URL = $linkage->addChild('gmd:URL', $data['distribution_url']);
                $name = $CI_OnlineResource->addChild('gmd:name');
                $name->addChild('gco:CharacterString', 'Download or access URL');
            }
            
            if ($data['service_url']) {
                $transferOptions = $MD_Distribution->addChild('gmd:transferOptions');
                $MD_DigitalTransferOptions = $transferOptions->addChild('gmd:MD_DigitalTransferOptions');
                $onLine = $MD_DigitalTransferOptions->addChild('gmd:onLine');
                $CI_OnlineResource = $onLine->addChild('gmd:CI_OnlineResource');
                $linkage = $CI_OnlineResource->addChild('gmd:linkage');
                $URL = $linkage->addChild('gmd:URL', $data['service_url']);
                $name = $CI_OnlineResource->addChild('gmd:name');
                $name->addChild('gco:CharacterString', 'Service URL');
            }
            
            if ($data['data_format']) {
                $distributionFormat = $MD_Distribution->addChild('gmd:distributionFormat');
                $MD_Format = $distributionFormat->addChild('gmd:MD_Format');
                $name = $MD_Format->addChild('gmd:name');
                $name->addChild('gco:CharacterString', is_array($data['data_format']) ? implode(', ', $data['data_format']) : $data['data_format']);
            }

            if ($data['coupled_resource']) {
                $transferOptions = $MD_Distribution->addChild('gmd:transferOptions');
                $MD_DigitalTransferOptions = $transferOptions->addChild('gmd:MD_DigitalTransferOptions');
                $onLine = $MD_DigitalTransferOptions->addChild('gmd:onLine');
                $CI_OnlineResource = $onLine->addChild('gmd:CI_OnlineResource');
                $name = $CI_OnlineResource->addChild('gmd:name');
                $name->addChild('gco:CharacterString', 'Coupled Resource: ' . $data['coupled_resource']);
            }
        }

        // INSPIRE metadata
        if ($data['conformity_result']) {
            $dataQualityInfo = $xml->addChild('gmd:dataQualityInfo');
            $DQ_DataQuality = $dataQualityInfo->addChild('gmd:DQ_DataQuality');
            $report = $DQ_DataQuality->addChild('gmd:report');
            $DQ_DomainConsistency = $report->addChild('gmd:DQ_DomainConsistency');
            $result = $DQ_DomainConsistency->addChild('gmd:result');
            $DQ_ConformanceResult = $result->addChild('gmd:DQ_ConformanceResult');
            $specification = $DQ_ConformanceResult->addChild('gmd:specification');
            $CI_Citation = $specification->addChild('gmd:CI_Citation');
            $title = $CI_Citation->addChild('gmd:title');
            $title->addChild('gco:CharacterString', 'INSPIRE Implementing Rules');
            $explanation = $DQ_ConformanceResult->addChild('gmd:explanation');
            $explanation->addChild('gco:CharacterString', $data['conformity_result']);
            $pass = $DQ_ConformanceResult->addChild('gmd:pass');
            $pass->addChild('gco:Boolean', $data['conformity_result'] === 'conformant' ? 'true' : 'false');
        }

        // Set response headers for XML display in browser
        $response = $response->withHeader('Content-Type', 'application/xml');
        // Remove the Content-Disposition header to display in browser instead of downloading
        
        // Output the XML
        $response->getBody()->write($xml->asXML());
        return $response;
        
    } catch (Exception $e) {
        error_log('Error in /metadata/{id}/xml endpoint: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// Add PDF export route
$app->get('/metadata/{id}/pdf', function (Request $request, Response $response, array $args) {
    try {
        $metadata = $this->get(Metadata::class);
        $data = $metadata->getById($args['id']);
        
        if (!$data) {
            throw new Exception('Metadata record not found');
        }

        // Create new PDF document
        $pdf = new GlobalTCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Novella GIS');
        $pdf->SetAuthor('Novella GIS');
        $pdf->SetTitle($data['title']);
        $pdf->SetSubject('Dataset Metadata');
	

        // Set default header data
        $pdf->SetHeaderData('', 0, 'Novella', $data['title']);

        // Set header and footer fonts
        $pdf->setHeaderFont(Array('helvetica', '', 12));
        $pdf->setFooterFont(Array('helvetica', '', 8));

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, 15);

        // Add a page
        $pdf->AddPage();

        // Set font
        $pdf->SetFont('helvetica', '', 10);

        // Function to add a section to the PDF
        $addSection = function($title, $content) use ($pdf) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, $title, 0, 1, 'L');
            $pdf->SetFont('helvetica', '', 10);
            $pdf->MultiCell(0, 6, $content, 0, 'L');
            $pdf->Ln(5);
        };

        // Basic Information
        $basicInfo = "Title: " . $data['title'] . "\n";
        if ($data['abstract']) {
            $basicInfo .= "Abstract: " . $data['abstract'] . "\n";
        }
        if ($data['purpose']) {
            $basicInfo .= "Purpose: " . $data['purpose'] . "\n";
        }
        if ($data['keywords']) {
            $keywords = is_array($data['keywords']) ? $data['keywords'] : explode(',', $data['keywords']);
            $basicInfo .= "Keywords: " . implode(', ', $keywords) . "\n";
        }
        if ($data['topic']) {
            $basicInfo .= "Topic: " . $data['topic'] . "\n";
        }
        if ($data['inspire_theme']) {
            $basicInfo .= "INSPIRE Theme: " . $data['inspire_theme'] . "\n";
        }
        $addSection('Basic Information', $basicInfo);

        // Citation Information
        $citationInfo = "";
        if ($data['citation_date']) {
            $citationInfo .= "Citation Date: " . $data['citation_date'] . "\n";
        }
        if ($data['responsible_org']) {
            $citationInfo .= "Responsible Organization: " . $data['responsible_org'] . "\n";
        }
        if ($data['responsible_person']) {
            $citationInfo .= "Responsible Person: " . $data['responsible_person'] . "\n";
        }
        if ($data['role']) {
            $citationInfo .= "Role: " . $data['role'] . "\n";
        }
        if ($citationInfo) {
            $addSection('Citation Information', $citationInfo);
        }

        // Spatial Information
        if (isset($data['west_longitude']) && isset($data['east_longitude']) && 
            isset($data['south_latitude']) && isset($data['north_latitude'])) {
            $spatialInfo = sprintf(
                "Spatial Extent:\nWest: %s°\nEast: %s°\nSouth: %s°\nNorth: %s°",
                $data['west_longitude'],
                $data['east_longitude'],
                $data['south_latitude'],
                $data['north_latitude']
            );
            if ($data['coordinate_system']) {
                $spatialInfo .= "\nCoordinate System: " . $data['coordinate_system'];
            }
            if ($data['spatial_resolution']) {
                $spatialInfo .= "\nSpatial Resolution: " . $data['spatial_resolution'];
            }
            $addSection('Spatial Information', $spatialInfo);
        }

        // Temporal Information
        if ($data['start_date'] || $data['end_date']) {
            $temporalInfo = "";
            if ($data['start_date']) {
                $temporalInfo .= "Start Date: " . $data['start_date'] . "\n";
            }
            if ($data['end_date']) {
                $temporalInfo .= "End Date: " . $data['end_date'] . "\n";
            }
            $addSection('Temporal Information', $temporalInfo);
        }

        // Constraints
        if ($data['use_constraints'] || $data['access_constraints'] || $data['use_limitation']) {
            $constraintsInfo = "";
            if ($data['use_constraints']) {
                $constraintsInfo .= "Use Constraints: " . $data['use_constraints'] . "\n";
            }
            if ($data['access_constraints']) {
                $constraintsInfo .= "Access Constraints: " . $data['access_constraints'] . "\n";
            }
            if ($data['use_limitation']) {
                $constraintsInfo .= "Use Limitation: " . $data['use_limitation'] . "\n";
            }
            $addSection('Constraints', $constraintsInfo);
        }

        // Distribution Information
        if ($data['distribution_url'] || $data['data_format']) {
            $distributionInfo = "";
            if ($data['distribution_url']) {
                $distributionInfo .= "Distribution URL: " . $data['distribution_url'] . "\n";
            }
            if ($data['data_format']) {
                $formats = is_array($data['data_format']) ? $data['data_format'] : explode(',', $data['data_format']);
                $distributionInfo .= "Data Format(s): " . implode(', ', $formats) . "\n";
            }
            $addSection('Distribution Information', $distributionInfo);
        }

        // INSPIRE Metadata
        if ($data['point_of_contact_org'] || $data['conformity_result'] || $data['spatial_data_service_url']) {
            $inspireInfo = "";
            if ($data['point_of_contact_org']) {
                $inspireInfo .= "INSPIRE Point of Contact: " . $data['point_of_contact_org'] . "\n";
            }
            if ($data['conformity_result']) {
                $inspireInfo .= "Conformity Result: " . $data['conformity_result'] . "\n";
            }
            if ($data['spatial_data_service_url']) {
                $inspireInfo .= "Spatial Data Service URL: " . $data['spatial_data_service_url'] . "\n";
            }
            $addSection('INSPIRE Metadata', $inspireInfo);
        }

        // Output the PDF
        $pdfContent = $pdf->Output('', 'S');
        
        // Set response headers for PDF
        $response = $response->withHeader('Content-Type', 'application/pdf');
        $response = $response->withHeader('Content-Disposition', 'inline; filename="metadata_' . $data['id'] . '.pdf"');
        
        // Write PDF content to response
        $response->getBody()->write($pdfContent);
        return $response;
        
    } catch (Exception $e) {
        error_log('Error in /metadata/{id}/pdf endpoint: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// Topics management routes
$app->get('/topics', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_topics')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    $topicsController = new TopicsController($this->get(PDO::class));
    $topics = $topicsController->index()['topics'];
    
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'topics.twig', [
        'topics' => $topics,
        'container_class' => 'container mx-auto'
    ]);
});

$app->post('/topics/add', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_topics')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    $data = $request->getParsedBody();
    $topicsController = new TopicsController($this->get(PDO::class));
    
    try {
        $id = $topicsController->add($data);
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Topic added successfully',
            'id' => $id
        ]));
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withStatus(500);
    }
    
    return $response->withHeader('Content-Type', 'application/json');
});

// Keywords management routes
$app->get('/keywords', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_keywords')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    $keywordsController = new KeywordsController($this->get(PDO::class));
    $keywords = $keywordsController->index()['keywords'];
    
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'keywords.twig', [
        'keywords' => $keywords,
        'container_class' => 'container mx-auto'
    ]);
});

$app->post('/harvest/settings', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_harvest')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    $data = $request->getParsedBody();
    $harvestSettings = new HarvestSettings($this->get(PDO::class));
    
    try {
        if (isset($data['id'])) {
            $result = $harvestSettings->update($data['id'], $data);
        } else {
            $result = $harvestSettings->create($data);
        }
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withStatus(500);
    }
});

$app->post('/harvest/start', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_harvest')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    try {
        $data = $request->getParsedBody();
        $harvestController = new HarvestController($this->get(PDO::class));
        $harvestSettings = new HarvestSettings($this->get(PDO::class));
        
        if (isset($data['id'])) {
            // Get specific harvest settings
            $settings = $harvestSettings->getById((int)$data['id']);
            if (!$settings) {
                throw new Exception('Harvest settings not found');
            }
        } else {
            // Get all settings and use the first one
            $settings = $harvestSettings->getAll();
            if (!$settings['success'] || empty($settings['data'])) {
                throw new Exception('No harvest settings found');
            }
            $settings = $settings['data'][0];
        }
        
        $result = $harvestController->startHarvest([
            'wms_url' => $settings['wms_url'],
            'layers' => $settings['layers']
        ]);
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error starting harvest: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response->withStatus(500);
    }
});

// Add new endpoint for fetching WMS layers
$app->post('/harvest/layers', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('manage_harvest')) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    try {
        $data = $request->getParsedBody();
        if (empty($data['wms_url'])) {
            throw new Exception('WMS URL is required');
        }

        $harvestController = new HarvestController($this->get(PDO::class));
        
        // Get WMS capabilities and parse layers
        $capabilities = $harvestController->getWmsCapabilities($data['wms_url']);
        $layers = $harvestController->parseWmsLayers($capabilities);
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'layers' => $layers
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error fetching WMS layers: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// Metadata update endpoint
$app->put('/metadata/{id}/update', function (Request $request, Response $response, array $args) {
    try {
        $metadataId = $args['id'];
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        // Debug logging
        error_log("Received update request for metadata ID: " . $metadataId);
        error_log("Raw request data: " . json_encode($data));
        error_log("Uploaded files: " . json_encode(array_keys($uploadedFiles)));
        
        // Get current dataset state
        $metadata = new \Novella\Models\Metadata($this->get(PDO::class));
        $currentDataset = $metadata->getById($metadataId);
        
        // Validate required fields
        if (empty($data['title']) || empty($data['abstract']) || empty($data['citation_date']) || empty($data['responsible_org'])) {
            throw new \Exception('Required fields are missing');
        }
        
        // Handle file uploads if present
        if (!empty($uploadedFiles)) {
            error_log("Processing file uploads...");
            
            // Handle thumbnail upload if present
            if (isset($uploadedFiles['thumbnail']) && $uploadedFiles['thumbnail']->getError() !== UPLOAD_ERR_NO_FILE) {
                $thumbnail = $uploadedFiles['thumbnail'];
                
                if ($thumbnail->getError() !== UPLOAD_ERR_OK) {
                    throw new Exception('Thumbnail upload failed: ' . getUploadErrorMessage($thumbnail->getError()));
                }

                // Create uploads directory if it doesn't exist
                $uploadDir = dirname(__DIR__) . '/storage/uploads/thumbnails';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $extension = pathinfo($thumbnail->getClientFilename(), PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $filepath = $uploadDir . '/' . $filename;

                // Move uploaded file
                $thumbnail->moveTo($filepath);

                // Update thumbnail info in gis_files table
                $db = $this->get(PDO::class);
                
                // First, delete any existing thumbnail entries
                $stmt = $db->prepare("DELETE FROM gis_files WHERE metadata_id = :metadata_id AND file_type = 'thumbnail'");
                $stmt->execute(['metadata_id' => $metadataId]);
                
                // Then insert the new thumbnail
                $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type, thumbnail_path) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type, :thumbnail_path)");
                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'file_name' => $thumbnail->getClientFilename(),
                    'file_type' => 'thumbnail',
                    'file_size' => $thumbnail->getSize(),
                    'file_path' => $filename,
                    'mime_type' => $thumbnail->getClientMediaType(),
                    'thumbnail_path' => $filename
                ]);
            }

            // Handle GIS files if present
            if (isset($uploadedFiles['gis_files'])) {
                foreach ($uploadedFiles['gis_files'] as $file) {
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        error_log('Error with GIS file: ' . getUploadErrorMessage($file->getError()));
                        continue;
                    }
                    
                    $metadata->deleteFiles($metadataId);

                    $uploadDir = dirname(__DIR__) . '/storage/uploads/';
                    $filepath = $uploadDir . '/'. uniqid() .'_'. $file->getClientFilename();
    
                    // Move uploaded file
                    $file->moveTo($filepath);
                    
                    // Insert file info into gis_files table
                    $db = $this->get(PDO::class);
                    $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type)");
                    $stmt->execute([
                        'metadata_id' => $metadataId,
                        'file_name' => $file->getClientFilename(),
                        'file_type' => pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
                        'file_size' => $file->getSize(),
                        'file_path' => $filepath,
                        'mime_type' => $file->getClientMediaType()
                    ]);
                }
            }
        }
        
        // Update the metadata
        $result = $metadata->update($metadataId, $data);
        error_log("Update result: " . json_encode($result));
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Metadata updated successfully',
            'id' => $metadataId
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error updating metadata: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// Also add a POST route to handle file uploads with _method=PUT
$app->post('/metadata/{id}/update', function (Request $request, Response $response, array $args) {
    try {
        $metadataId = $args['id'];
        $data = $request->getParsedBody();
        $uploadedFiles = $request->getUploadedFiles();
        
        // Debug logging
        error_log("Received POST update request for metadata ID: " . $metadataId);
        error_log("Raw request data: " . json_encode($data));
        error_log("Uploaded files: " . json_encode(array_keys($uploadedFiles)));
        
        // Get current dataset state
        $metadata = new \Novella\Models\Metadata($this->get(PDO::class));
        $currentDataset = $metadata->getById($metadataId);
        
        // Validate required fields
        if (empty($data['title']) || empty($data['abstract']) || empty($data['citation_date']) || empty($data['responsible_org'])) {
            throw new \Exception('Required fields are missing');
        }
        
        // Handle file uploads if present
        if (!empty($uploadedFiles)) {
            error_log("Processing file uploads...");
            
            // Handle thumbnail upload if present
            if (isset($uploadedFiles['thumbnail']) && $uploadedFiles['thumbnail']->getError() !== UPLOAD_ERR_NO_FILE) {
                $thumbnail = $uploadedFiles['thumbnail'];
                
                if ($thumbnail->getError() !== UPLOAD_ERR_OK) {
                    throw new Exception('Thumbnail upload failed: ' . getUploadErrorMessage($thumbnail->getError()));
                }

                // Create uploads directory if it doesn't exist
                $uploadDir = dirname(__DIR__) . '/storage/uploads/thumbnails';
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                // Generate unique filename
                $extension = pathinfo($thumbnail->getClientFilename(), PATHINFO_EXTENSION);
                $filename = uniqid() . '.' . $extension;
                $filepath = $uploadDir . '/' . $filename;

                // Move uploaded file
                $thumbnail->moveTo($filepath);

                // Update thumbnail info in gis_files table
                $db = $this->get(PDO::class);
                
                // First, delete any existing thumbnail entries
                $stmt = $db->prepare("DELETE FROM gis_files WHERE metadata_id = :metadata_id AND file_type = 'thumbnail'");
                $stmt->execute(['metadata_id' => $metadataId]);
                
                // Then insert the new thumbnail
                $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type, thumbnail_path) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type, :thumbnail_path)");
                $stmt->execute([
                    'metadata_id' => $metadataId,
                    'file_name' => $thumbnail->getClientFilename(),
                    'file_type' => 'thumbnail',
                    'file_size' => $thumbnail->getSize(),
                    'file_path' => $filename,
                    'mime_type' => $thumbnail->getClientMediaType(),
                    'thumbnail_path' => $filename
                ]);
            }

            // Handle GIS files if present
            if (isset($uploadedFiles['gis_files'])) {
                foreach ($uploadedFiles['gis_files'] as $file) {
                    if ($file->getError() !== UPLOAD_ERR_OK) {
                        error_log('Error with GIS file: ' . getUploadErrorMessage($file->getError()));
                        continue;
                    }
                    
                    $metadata->deleteFiles($metadataId);
                    
                    $uploadDir = dirname(__DIR__) . '/storage/uploads/';
                    $filepath = $uploadDir . '/'. uniqid() .'_'. $file->getClientFilename();
    
                    // Move uploaded file
                    $file->moveTo($filepath);

                    // Insert file info into gis_files table
                    $db = $this->get(PDO::class);
                    $stmt = $db->prepare("INSERT INTO gis_files (metadata_id, file_name, file_type, file_size, file_path, mime_type) VALUES (:metadata_id, :file_name, :file_type, :file_size, :file_path, :mime_type)");
                    $stmt->execute([
                        'metadata_id' => $metadataId,
                        'file_name' => $file->getClientFilename(),
                        'file_type' => pathinfo($file->getClientFilename(), PATHINFO_EXTENSION),
                        'file_size' => $file->getSize(),
                        'file_path' => $filepath,
                        'mime_type' => $file->getClientMediaType()
                    ]);
                }
            }
        }
        
        // Update the metadata
        $result = $metadata->update($metadataId, $data);
        error_log("Update result: " . json_encode($result));
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Metadata updated successfully',
            'id' => $metadataId
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error updating metadata: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// WMS capabilities endpoint
$app->post('/wms/capabilities', function (Request $request, Response $response) {
    $wmsController = new \Novella\Controllers\WmsController();
    return $wmsController->getCapabilities($request, $response);
});

// Spatial search endpoint
$app->post('/api/datasets/search-by-bbox', function (Request $request, Response $response) {
    try {
        error_log("Received search-by-bbox request");
        error_log("Request headers: " . json_encode($request->getHeaders()));
        error_log("Request body: " . json_encode($request->getParsedBody()));
        
        $data = $request->getParsedBody();
        if (!isset($data['bbox']) || !isset($data['spatialRelation'])) {
            error_log("Missing required parameters in request");
            throw new Exception('Missing required parameters: bbox and spatialRelation');
        }

        $bbox = $data['bbox'];
        $spatialRelation = $data['spatialRelation'];

        // Validate bbox
        $requiredKeys = ['west', 'south', 'east', 'north'];
        foreach ($requiredKeys as $key) {
            if (!isset($bbox[$key]) || !is_numeric($bbox[$key])) {
                throw new Exception("Invalid bbox: missing or invalid {$key} coordinate");
            }
        }

        // Validate geographic coordinates
        if ($bbox['west'] < -180 || $bbox['west'] > 180 ||
            $bbox['east'] < -180 || $bbox['east'] > 180 ||
            $bbox['south'] < -90 || $bbox['south'] > 90 ||
            $bbox['north'] < -90 || $bbox['north'] > 90) {
            throw new Exception("Invalid geographic coordinates: coordinates must be within valid ranges (longitude: -180 to 180, latitude: -90 to 90)");
        }

        // Validate bbox order
        if ($bbox['west'] >= $bbox['east'] || $bbox['south'] >= $bbox['north']) {
            throw new Exception("Invalid bounding box: west must be less than east, and south must be less than north");
        }

        // Round coordinates to 6 decimal places
        $bbox = array_map(function($value) {
            return round((float)$value, 6);
        }, $bbox);

        // Get metadata model
        $metadata = $this->get(Metadata::class);
        error_log("Metadata model retrieved, searching for datasets");
        
        // Search for datasets based on spatial relation
        $datasets = $metadata->searchByBbox($bbox, $spatialRelation);
        error_log("Found " . count($datasets) . " datasets");
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'datasets' => $datasets
        ]));
        error_log("Sending successful response");
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log("Error in spatial search: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        error_log("Request URI: " . $request->getUri()->__toString());
        error_log("Request method: " . $request->getMethod());
        error_log("Request headers: " . json_encode($request->getHeaders()));
        
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

$app->post('/datasets/{id}/toggle-public', function (Request $request, Response $response, array $args) {
    $auth = $this->get('auth');
    if (!$auth->getCurrentUser()->hasPermission('edit_dataset')) {
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => 'Permission denied'
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }

    try {
        $metadata = $this->get(Metadata::class);
        $result = $metadata->togglePublic($args['id']);
        
        $response->getBody()->write(json_encode($result));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error toggling dataset public status: ' . $e->getMessage());
        $response->getBody()->write(json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

// User management routes
$app->get('/users', function (Request $request, Response $response) {
    $auth = $this->get('auth');
    $auth->requireRole('admin');

    $usersController = new UsersController($this->get(PDO::class));
    $data = $usersController->index();
    $roles = $usersController->getRoles();
    
    $twig = $this->get(Twig::class);
    return $twig->render($response, 'users.twig', array_merge($data, $roles));
});

// User management API endpoints
$app->group('/api/users', function (RouteCollectorProxy $group) {
    $group->get('/{id}', function (Request $request, Response $response, array $args) {
        $auth = $this->get('auth');
        $auth->requireRole('admin');

        $usersController = new UsersController($this->get(PDO::class));
        try {
            $user = $usersController->getById((int)$args['id']);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'user' => $user
            ]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(404);
        }
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->post('', function (Request $request, Response $response) {
        $auth = $this->get('auth');
        $auth->requireRole('admin');

        $data = $request->getParsedBody();
        $usersController = new UsersController($this->get(PDO::class));
        
        try {
            $userId = $usersController->create($data);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User created successfully',
                'id' => $userId
            ]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->put('/{id}', function (Request $request, Response $response, array $args) {
        $auth = $this->get('auth');
        $auth->requireRole('admin');

        $data = $request->getParsedBody();
        $usersController = new UsersController($this->get(PDO::class));
        
        try {
            $usersController->update((int)$args['id'], $data);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User updated successfully'
            ]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    });

    $group->delete('/{id}', function (Request $request, Response $response, array $args) {
        $auth = $this->get('auth');
        $auth->requireRole('admin');

        $usersController = new UsersController($this->get(PDO::class));
        
        try {
            $usersController->delete((int)$args['id']);
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ]));
        } catch (Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400);
        }
        return $response->withHeader('Content-Type', 'application/json');
    });
});

// Harvest management routes
$app->group('', function (RouteCollectorProxy $group) {
    // Harvest routes
    $group->get('/harvest', function (Request $request, Response $response) {
        $auth = $this->get('auth');
        if (!$auth->getCurrentUser()->hasPermission('manage_harvest')) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Permission denied'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        $harvestController = new HarvestController($this->get(PDO::class));
        $harvestSettings = new HarvestSettings($this->get(PDO::class));
        
        $settingsResult = $harvestSettings->getAll();
        if (!$settingsResult['success']) {
            throw new Exception($settingsResult['message'] ?? 'Failed to get harvest settings');
        }
        $settings = $settingsResult['data'];
        $harvests = $harvestController->getHarvestHistory();
        
        $twig = $this->get(Twig::class);
        return $twig->render($response, 'harvest.twig', [
            'settings' => $settings,
            'harvests' => $harvests,
            'container_class' => 'container mx-auto'
        ]);
    });

    // Get a single harvest setting
    $group->get('/harvest/settings/{id}', function (Request $request, Response $response, array $args) {
        try {
            $id = (int)$args['id'];
            $harvestSettings = $this->get(HarvestSettings::class);
            $setting = $harvestSettings->getById($id);
            
            if (!$setting) {
                $response->getBody()->write(json_encode([
                    'status' => 'error',
                    'message' => 'Harvest settings not found'
                ]));
                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(404);
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => $setting
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            error_log('Error in GET /harvest/settings/{id}: ' . $e->getMessage());
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    $group->delete('/harvest/settings/{id}', function (Request $request, Response $response, array $args) {
        try {
            $id = (int)$args['id'];
            $harvestSettings = $this->get(HarvestSettings::class);
            $result = $harvestSettings->delete($id);
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            error_log('Error in DELETE /harvest/settings/{id}: ' . $e->getMessage());
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    // Run harvest immediately
    $group->post('/harvest/settings/{id}/run', function (Request $request, Response $response, array $args) {
        $auth = $this->get('auth');
        if (!$auth->getCurrentUser()->hasPermission('manage_harvest')) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Permission denied'
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(403);
        }

        try {
            $id = (int)$args['id'];
            $harvestSettings = $this->get(HarvestSettings::class);
            $setting = $harvestSettings->getById($id);
            
            if (!$setting) {
                throw new Exception('Harvest settings not found');
            }

            $harvestController = new HarvestController($this->get(PDO::class));
            $result = $harvestController->startHarvest([
                'wms_url' => $setting['wms_url'],
                'layers' => $setting['layers']
            ]);
            
            $response->getBody()->write(json_encode($result));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (Exception $e) {
            error_log('Error starting harvest: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(500);
        }
    });

    // Extract spatial extent from uploaded GIS files
    $group->post('/metadata/extract-spatial-extent', function (Request $request, Response $response) {
        try {
            $uploadedFiles = $request->getUploadedFiles();
            if (empty($uploadedFiles['gis_files'])) {
                throw new Exception('No files uploaded');
            }

            $gisFiles = $uploadedFiles['gis_files'];
            if (!is_array($gisFiles)) {
                $gisFiles = [$gisFiles];
            }

            $tempDir = sys_get_temp_dir();
            $metadata = [
                'west_longitude' => null,
                'east_longitude' => null,
                'south_latitude' => null,
                'north_latitude' => null,
                'coordinate_system' => null
            ];

            foreach ($gisFiles as $file) {
                if ($file->getError() !== UPLOAD_ERR_OK) {
                    throw new Exception('File upload error: ' . $file->getError());
                }

                $filename = $file->getClientFilename();
                $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $tempPath = $tempDir . '/' . uniqid() . '_' . $filename;
                $file->moveTo($tempPath);

                try {
                    $fileToProcess = $tempPath;
                    $extractDir = null;

                    // Handle ZIP files (shapefiles,qgis project)
                    if ($fileExtension === 'zip') {
                        error_log("Processing ZIP file: " . $filename);
                        $zip = new ZipArchive();
                        $zipResult = $zip->open($tempPath);
                        
                        if ($zipResult !== true) {
                            throw new Exception('Failed to open ZIP file: ' . $zipResult);
                        }

                        // Create a temporary directory for extraction
                        $extractDir = $tempDir . '/' . uniqid() . '_extract';
                        if (!mkdir($extractDir, 0777, true)) {
                            throw new Exception('Failed to create extraction directory');
                        }

                        // Extract the ZIP file
                        $zip->extractTo($extractDir);
                        $zip->close();

                        // Find the .qgs/.shp file
                        $iterator = new RecursiveIteratorIterator(
                            new RecursiveDirectoryIterator($extractDir, RecursiveDirectoryIterator::SKIP_DOTS)
                        );
                        
                        $fileToProcess = find_file_by_type($extractDir, 'qgs');
                        if($fileToProcess == null){
                            $fileToProcess = find_file_by_type($extractDir, 'shp');
                        }

                        if (!$fileToProcess) {
                            throw new Exception('No .qgs/.shp file found in the ZIP archive');
                        }
                    }
    
                    // Process the file (either extracted shapefile or original file)
                    if ($fileExtension === 'zip') {
                        // For shapefiles, use OGR
                        $ogrinfoCommand = 'ogrinfo -so -json ' . escapeshellarg($fileToProcess);
                        error_log("Executing OGR command: " . $ogrinfoCommand);
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

                                // Get coordinate system from the JSON
                                if (isset($info['layers'][0]['geometryFields'][0]['coordinateSystem']['wkt'])) {
                                    $wkt = $info['layers'][0]['geometryFields'][0]['coordinateSystem']['wkt'];
                                    if (preg_match('/EPSG["\s:]+(\d+)/i', $wkt, $matches)) {
                                        $metadata['coordinate_system'] = 'EPSG:' . $matches[1];
                                        error_log("Found coordinate system from OGR JSON: " . $metadata['coordinate_system']);
                                    }
                                }
                            }
                        }
                    } else {
                        // For other files (rasters), use GDAL
                        $gdalinfoCommand = 'gdalinfo -json ' . escapeshellarg($fileToProcess);
                        error_log("Executing GDAL command: " . $gdalinfoCommand);
                        $output = shell_exec($gdalinfoCommand . ' 2>&1');
                        error_log("GDAL output: " . ($output ?: "No output"));

                        if ($output) {
                            $info = json_decode($output, true);
                            if ($info) {
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
                                    
                                    // Get coordinate system
                                    if (isset($info['coordinateSystem']['wkt'])) {
                                        $wkt = $info['coordinateSystem']['wkt'];
                                        if (preg_match('/EPSG["\s:]+(\d+)/i', $wkt, $matches)) {
                                            $metadata['coordinate_system'] = 'EPSG:' . $matches[1];
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
                                    }
                                }
                            }
                        }
                    }

                    // If we have a coordinate system that's not EPSG:4326, transform the coordinates
                    if (!empty($metadata['coordinate_system']) && $metadata['coordinate_system'] !== 'EPSG:4326' && 
                        $metadata['west_longitude'] !== null) {
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

                    // Round coordinates to 6 decimal places
                    if ($metadata['west_longitude'] !== null) {
                        $metadata['west_longitude'] = round($metadata['west_longitude'], 6);
                        $metadata['east_longitude'] = round($metadata['east_longitude'], 6);
                        $metadata['south_latitude'] = round($metadata['south_latitude'], 6);
                        $metadata['north_latitude'] = round($metadata['north_latitude'], 6);
                    }

                } finally {
                    // Clean up temporary files
                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }
                    if ($extractDir && file_exists($extractDir)) {
                        removeDirectory($extractDir);
                    }
                }
            }

            if ($metadata['west_longitude'] === null) {
                throw new Exception('Could not extract spatial extent from the uploaded files');
            }

            $proto = empty($_SERVER['HTTPS']) ? 'http' : 'https';

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'spatial_extent' => $metadata
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (Exception $e) {
            error_log('Error extracting spatial extent: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(500);
        }
    });

    // Helper function to recursively remove a directory
    function removeDirectory($dir) {
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
    
    function find_file_by_type($dirpath, $ext){
        // Find the .qgs/.shp file
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dirpath, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        $fileToProcess = null;
        foreach ($iterator as $file) {
            if (strtolower($file->getExtension()) === $ext) {
                $fileToProcess = $file->getPathname();
                error_log("Found ".$ext." file: " . $fileToProcess);
                break;
            }
        }
        return $fileToProcess;
    }

    // ... rest of the routes ...
});

// Spatial extent extraction endpoint is now handled in the group routes above
// $app->post('/metadata/extract-spatial-extent', function (Request $request, Response $response) use ($app) {
//     // ... existing code ...
// });

// Add this new endpoint after the other dataset routes
$app->get('/api/datasets', function (Request $request, Response $response) {
    try {
        $metadata = $this->get(Metadata::class);
        $queryParams = $request->getQueryParams();
        
        // Get pagination parameters
        $page = max(1, intval($queryParams['page'] ?? 1));
        $perPage = max(1, min(100, intval($queryParams['per_page'] ?? 20))); // Limit max items per page to 100
        
        // Get search and filter parameters
        $searchTerm = $queryParams['search'] ?? '';
        $topicId = $queryParams['topic'] ?? null;
        $keyword = $queryParams['keyword'] ?? null;
        $dateFrom = $queryParams['date_from'] ?? null;
        $dateTo = $queryParams['date_to'] ?? null;
        
        error_log("Received request to /api/datasets");
        error_log("Query parameters: " . json_encode($queryParams));
        
        // If we have any search/filter parameters, use the search method
        if (!empty($searchTerm) || !empty($topicId) || !empty($keyword) || !empty($dateFrom) || !empty($dateTo)) {
            $result = $metadata->search($searchTerm, $topicId, $keyword, $dateFrom, $dateTo, $page, $perPage);
        } else {
            $result = $metadata->getAll($page, $perPage);
        }
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'datasets' => $result['datasets'],
            'pagination' => $result['pagination']
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error fetching datasets: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(500);
    }
});

$app->get('/api/datasets/by-ids', function (Request $request, Response $response) {
    try {
        $metadata = $this->get(Metadata::class);
        $queryParams = $request->getQueryParams();
        
        error_log("Received request to /api/datasets/by-ids");
        error_log("Query parameters: " . json_encode($queryParams));
        
        if (empty($queryParams['ids'])) {
            error_log("No dataset IDs provided in request");
            throw new Exception('No dataset IDs provided');
        }
        
        $ids = explode(',', $queryParams['ids']);
        error_log("Parsed dataset IDs: " . json_encode($ids));
        
        // Validate that all IDs are valid UUIDs
        foreach ($ids as $id) {
            if (!preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $id)) {
                error_log("Invalid dataset ID format: " . $id);
                throw new Exception("Invalid dataset ID format: " . $id);
            }
        }
        
        $isSpatialSearch = filter_var($queryParams['spatial_search'] ?? false, FILTER_VALIDATE_BOOLEAN);
        error_log("Is spatial search: " . ($isSpatialSearch ? 'true' : 'false'));
        
        // Get datasets by IDs
        $datasets = $metadata->getByIds($ids, $isSpatialSearch);
        error_log("Found " . count($datasets) . " datasets");
        
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'datasets' => $datasets
        ]));
        
        return $response->withHeader('Content-Type', 'application/json');
    } catch (Exception $e) {
        error_log('Error fetching datasets by IDs: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
});

// Run app
$app->run();
