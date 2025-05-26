<?php

declare(strict_types=1);

namespace GeoLibre\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Views\Twig;

class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Twig $twig
    ) {
        // Generate CSRF token if it doesn't exist
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Add CSRF token to Twig
        $this->twig->getEnvironment()->addGlobal('csrf_token', $_SESSION['csrf_token']);
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        // Only validate POST, PUT, DELETE requests
        if (in_array($request->getMethod(), ['POST', 'PUT', 'DELETE'])) {
            $token = $request->getParsedBody()['csrf'] ?? null;
            
            if (!$token || $token !== $_SESSION['csrf_token']) {
                throw new \Exception('Invalid CSRF token');
            }
        }

        return $handler->handle($request);
    }
} 