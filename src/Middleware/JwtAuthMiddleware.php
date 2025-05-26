<?php

declare(strict_types=1);

namespace GeoLibre\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use GeoLibre\Service\JwtService;

class JwtAuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private JwtService $jwtService
    ) {}

    public function process(Request $request, RequestHandler $handler): Response
    {
        $authHeader = $request->getHeaderLine('Authorization');
        
        if (empty($authHeader)) {
            return $this->unauthorized('No authorization header');
        }

        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->unauthorized('Invalid authorization header format');
        }

        $token = $matches[1];
        $payload = $this->jwtService->validateToken($token);

        if (!$payload) {
            return $this->unauthorized('Invalid or expired token');
        }

        // Add user data to request attributes
        $request = $request->withAttribute('user', $payload);
        
        return $handler->handle($request);
    }

    private function unauthorized(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => $message
        ]));
        return $response
            ->withStatus(401)
            ->withHeader('Content-Type', 'application/json')
            ->withHeader('WWW-Authenticate', 'Bearer');
    }
} 