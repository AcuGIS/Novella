<?php
declare(strict_types=1);

// Configure session
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
ini_set('session.cookie_samesite', 'Lax');
ini_set('session.gc_maxlifetime', 3600); // 1 hour

// Start session before anything else
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug session
error_log("Session started: " . session_id());
error_log("Session status: " . session_status());
error_log("Session data: " . print_r($_SESSION, true));

// Basic debugging
file_put_contents('/tmp/geolibre_debug.log', "Request received at " . date('Y-m-d H:i:s') . "\n", FILE_APPEND);
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Script Name: " . $_SERVER['SCRIPT_NAME']);
error_log("PHP Self: " . $_SERVER['PHP_SELF']);
error_log("Document Root: " . $_SERVER['DOCUMENT_ROOT']);
error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);
error_log("Query String: " . ($_SERVER['QUERY_STRING'] ?? 'none'));
error_log("HTTP Host: " . $_SERVER['HTTP_HOST']);
error_log("HTTP User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'none'));
error_log("HTTP Accept: " . ($_SERVER['HTTP_ACCEPT'] ?? 'none'));

use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;
use GeoLibre\Controller\HomeController;
use GeoLibre\Controller\HarvestController;
use GeoLibre\Controller\OaiPmhController;
use GeoLibre\Controller\CatalogController;
use GeoLibre\Controller\MetadataController;
use Twig\Loader\FilesystemLoader;
use Slim\Csrf\Guard as CsrfMiddleware;
use GeoLibre\Middleware\AuthMiddleware;
use GeoLibre\Middleware\AdminMiddleware;
use Psr\Http\Server\RequestHandlerInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Psr\Log\LoggerInterface;

// Add debugging
error_log("OAI-PMH request received");
error_reporting(E_ALL);
ini_set('display_errors', 1);

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

error_log("Creating container builder");
// Create Container
$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    // Configure settings
    'settings' => function () {
        error_log("Loading settings");
        return require __DIR__ . '/../config/settings.php';
    },

    // Configure logger
    LoggerInterface::class => function () {
        $logger = new Logger('geolibre');
        $logPath = __DIR__ . '/../logs/app.log';
        if (!is_dir(dirname($logPath))) {
            mkdir(dirname($logPath), 0777, true);
        }
        $logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        return $logger;
    },

    // Configure database connection
    'Doctrine\DBAL\Connection' => function ($container) {
        error_log("Creating database connection");
        $settings = $container->get('settings');
        $dbSettings = $settings['db'];
        $params = [
            'driver' => 'pdo_pgsql',
            'host' => $dbSettings['host'],
            'port' => $dbSettings['port'],
            'dbname' => $dbSettings['database'],
            'user' => $dbSettings['username'],
            'password' => $dbSettings['password'],
            'charset' => 'utf8'
        ];
        return \Doctrine\DBAL\DriverManager::getConnection($params);
    },

    // Configure Twig loader
    \Twig\Loader\LoaderInterface::class => function () {
        error_log("Creating Twig loader");
        return new FilesystemLoader(__DIR__ . '/../src/View');
    },

    // Configure Twig view
    Twig::class => function ($container) {
        error_log("Creating Twig view");
        $settings = $container->get('settings');
        $isProd = ($settings['app']['env'] ?? 'production') === 'production';
        $twig = new Twig(
            $container->get(\Twig\Loader\LoaderInterface::class),
            [
                'cache' => $isProd ? __DIR__ . '/../var/cache/twig' : false,
                'auto_reload' => !$isProd,
                'debug' => !$isProd
            ]
        );
        
        // Add custom extensions
        $twig->addExtension(new \GeoLibre\View\TwigExtension());
        
        return $twig;
    },

    // Configure models
    GeoLibre\Model\GisData::class => function ($container) {
        error_log("Creating GisData model");
        return new GeoLibre\Model\GisData($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\Metadata::class => function ($container) {
        error_log("Creating Metadata model");
        return new GeoLibre\Model\Metadata($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Validator\GisDataValidator::class => function ($container) {
        error_log("Creating GisDataValidator");
        return new GeoLibre\Validator\GisDataValidator();
    },
    GeoLibre\Model\OaiPmh::class => function ($container) {
        error_log("Creating OaiPmh model");
        return new GeoLibre\Model\OaiPmh($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\HarvestSource::class => function ($container) {
        error_log("Creating HarvestSource model");
        return new GeoLibre\Model\HarvestSource($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\Dataset::class => function ($container) {
        error_log("Creating Dataset model");
        return new GeoLibre\Model\Dataset($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\MetadataTemplate::class => function ($container) {
        error_log("Creating MetadataTemplate model");
        return new GeoLibre\Model\MetadataTemplate($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\User::class => function ($container) {
        error_log("Creating User model");
        return new GeoLibre\Model\User($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\Topic::class => function ($container) {
        error_log("Creating Topic model");
        return new GeoLibre\Model\Topic($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Model\Document::class => function ($container) {
        error_log("Creating Document model");
        return new GeoLibre\Model\Document($container->get('Doctrine\DBAL\Connection'));
    },
    GeoLibre\Service\DocumentService::class => function ($container) {
        error_log("Creating DocumentService");
        $settings = $container->get('settings');
        $uploadDir = __DIR__ . '/../storage/documents';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        return new GeoLibre\Service\DocumentService(
            $uploadDir,
            $container->get(GeoLibre\Model\Document::class)
        );
    },
    GeoLibre\Controller\DocumentController::class => function ($container) {
        error_log("Creating DocumentController");
        return new GeoLibre\Controller\DocumentController(
            $container->get(GeoLibre\Model\Document::class),
            $container->get(GeoLibre\Service\DocumentService::class),
            $container->get(Twig::class),
            $container->get(LoggerInterface::class),
            $container->get(GeoLibre\Model\User::class)
        );
    },

    // Configure JWT service
    GeoLibre\Service\JwtService::class => function ($container) {
        error_log("Creating JwtService");
        $settings = $container->get('settings');
        return new GeoLibre\Service\JwtService(
            $settings['jwt']['secret'] ?? $_ENV['JWT_SECRET'] ?? 'your-secret-key', // Use environment variable or fallback
            $settings['jwt']['algorithm'] ?? 'HS256',
            $settings['jwt']['expiry'] ?? 3600
        );
    },

    // Configure JWT middleware
    GeoLibre\Middleware\JwtAuthMiddleware::class => function ($container) {
        error_log("Creating JwtAuthMiddleware");
        return new GeoLibre\Middleware\JwtAuthMiddleware(
            $container->get(GeoLibre\Service\JwtService::class)
        );
    },

    // Configure API Auth Controller
    GeoLibre\Controller\ApiAuthController::class => function ($container) {
        error_log("Creating ApiAuthController");
        return new GeoLibre\Controller\ApiAuthController(
            $container->get(GeoLibre\Model\User::class),
            $container->get(GeoLibre\Service\JwtService::class)
        );
    },

    // Configure controllers
    CatalogController::class => function ($container) {
        error_log("Creating CatalogController");
        return new CatalogController(
            $container->get(GeoLibre\Model\GisData::class),
            $container->get(GeoLibre\Model\Metadata::class),
            $container->get(GeoLibre\Model\Topic::class),
            $container->get(GeoLibre\Validator\GisDataValidator::class),
            $container->get(Twig::class),
            $container->get(LoggerInterface::class),
            $container->get(GeoLibre\Model\Document::class),
            $container->get(GeoLibre\Model\MetadataTemplate::class)
        );
    },
    HomeController::class => function ($container) {
        error_log("Creating HomeController");
        return new HomeController($container->get(Twig::class));
    },
    HarvestController::class => function ($container) {
        error_log("Creating HarvestController");
        return new HarvestController(
            $container->get(GeoLibre\Model\HarvestSource::class),
            $container->get(GeoLibre\Model\OaiPmh::class),
            $container->get(Twig::class),
            $container->get('Doctrine\DBAL\Connection'),
            $container->get(GeoLibre\Model\Metadata::class)
        );
    },
    OaiPmhController::class => function ($container) {
        error_log("Creating OaiPmhController");
        $settings = $container->get('settings');
        return new OaiPmhController(
            $container->get(GeoLibre\Model\OaiPmh::class),
            [
                'repository_name' => $settings['oai']['repository_name'],
                'base_url' => $settings['app']['url'] . '/oai',
                'admin_email' => $settings['oai']['admin_email'],
            ]
        );
    },
    MetadataController::class => function ($container) {
        error_log("Creating MetadataController");
        return new MetadataController(
            $container->get(GeoLibre\Model\Metadata::class),
            $container->get(GeoLibre\Model\Dataset::class),
            $container->get(GeoLibre\Model\MetadataTemplate::class),
            $container->get(Twig::class),
            $container->get(GeoLibre\Model\GisData::class)
        );
    }
]);

error_log("Building container");
// Build PHP-DI Container instance
$container = $containerBuilder->build();

error_log("Setting container on AppFactory");
// Set container to create App with on AppFactory
AppFactory::setContainer($container);

error_log("Creating app");
// Create App
$app = AppFactory::create();

error_log("Adding error middleware");
// Add Error Middleware
$app->addErrorMiddleware(true, true, true);

error_log("Adding body parsing middleware");
// Add Body Parsing Middleware
$app->addBodyParsingMiddleware();

error_log("Setting base path");
// Set base path if needed
$app->setBasePath('');

error_log("Loading routes");
// Add routes
$routes = require __DIR__ . '/../config/routes.php';
$routes($app);

error_log("Adding Twig middleware");
// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app, Twig::class));

error_log("Running app");
// Run app
$app->run(); 