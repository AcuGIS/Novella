<?php

declare(strict_types=1);

namespace GeoLibre\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AuthMiddleware implements MiddlewareInterface
{
    private array $publicPaths = [
        '/auth/login',
        '/auth/logout',
        '/api/public',
        '/oai'  // Only the base OAI-PMH endpoint is public
    ];

    public function process(Request $request, RequestHandler $handler): Response
    {
        $path = $request->getUri()->getPath();
        error_log("AuthMiddleware: Processing request for path: " . $path);
        error_log("AuthMiddleware: Session ID: " . session_id());
        error_log("AuthMiddleware: Session data: " . print_r($_SESSION, true));

        // Check if this is a public path
        foreach ($this->publicPaths as $publicPath) {
            if ($path === $publicPath || $path === $publicPath . '/') {
                error_log("AuthMiddleware: Public path detected, skipping authentication");
                return $handler->handle($request);
            }
        }

        // Special handling for OAI-PMH base endpoint
        if ($path === '/oai' || $path === '/oai/') {
            error_log("AuthMiddleware: OAI-PMH base endpoint detected, skipping authentication");
            return $handler->handle($request);
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            error_log("AuthMiddleware: No user_id in session, redirecting to public datasets page");
            $_SESSION['redirect_after_login'] = (string) $request->getUri();
            $response = new SlimResponse();
            return $response->withHeader('Location', '/public')->withStatus(302);
        }

        error_log("AuthMiddleware: User is logged in, proceeding with request");
        return $handler->handle($request);
    }
} 