<?php

declare(strict_types=1);

namespace GeoLibre\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class HarvesterMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $user = $_SESSION['user'] ?? null;
        
        error_log("HarvesterMiddleware: User session data: " . print_r($user, true));
        
        if (!$user) {
            // Not logged in
            error_log("HarvesterMiddleware: No user in session");
            $response = new SlimResponse();
            return $response->withHeader('Location', '/auth/login')->withStatus(302);
        }

        // Check if user is in Admin or Publisher group
        error_log("HarvesterMiddleware: Checking group_name: " . ($user['group_name'] ?? 'not set'));
        if ($user['group_name'] !== 'Admin' && $user['group_name'] !== 'Publisher') {
            // User is not authorized
            error_log("HarvesterMiddleware: User not authorized. Group: " . ($user['group_name'] ?? 'not set'));
            $response = new SlimResponse();
            $_SESSION['flash'] = [
                'type' => 'error',
                'message' => 'You do not have permission to access the harvester. Only Admin and Publisher users can access this feature.'
            ];
            return $response->withHeader('Location', '/')->withStatus(302);
        }

        error_log("HarvesterMiddleware: User authorized with group: " . $user['group_name']);
        // User is authorized, proceed with the request
        return $handler->handle($request);
    }
} 