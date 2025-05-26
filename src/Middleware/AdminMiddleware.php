<?php

declare(strict_types=1);

namespace GeoLibre\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class AdminMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        error_log("AdminMiddleware: Processing request");
        error_log("AdminMiddleware: Session data: " . print_r($_SESSION, true));

        // First check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            error_log("AdminMiddleware: No user_id in session");
            $_SESSION['redirect_after_login'] = (string) $request->getUri();
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/auth/login')
                ->withStatus(302);
        }

        // Then check if user is admin
        if (!isset($_SESSION['user']['roles'])) {
            error_log("AdminMiddleware: No roles in user session data");
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        if (!in_array('ROLE_ADMIN', $_SESSION['user']['roles'])) {
            error_log("AdminMiddleware: User does not have ROLE_ADMIN. Roles: " . print_r($_SESSION['user']['roles'], true));
            $response = new SlimResponse();
            return $response
                ->withHeader('Location', '/')
                ->withStatus(302);
        }

        error_log("AdminMiddleware: User has admin role, proceeding");
        return $handler->handle($request);
    }
} 