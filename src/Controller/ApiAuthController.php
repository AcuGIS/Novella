<?php

declare(strict_types=1);

namespace GeoLibre\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use GeoLibre\Model\User;
use GeoLibre\Service\JwtService;

class ApiAuthController
{
    public function __construct(
        private User $user,
        private JwtService $jwtService
    ) {}

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['username']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Username and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $user = $this->user->findByUsername($data['username']);
            if (!$user || !password_verify($data['password'], $user['password'])) {
                throw new \Exception('Invalid credentials');
            }

            $token = $this->jwtService->generateToken([
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'roles' => $user['roles'] ?? ['ROLE_USER']
            ]);
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'roles' => $user['roles'] ?? ['ROLE_USER']
                    ]
                ]
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Username, email and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            // Check if username already exists
            if ($this->user->findByUsername($data['username'])) {
                throw new \Exception('Username already exists');
            }

            // Check if email already exists
            if ($this->user->findByEmail($data['email'])) {
                throw new \Exception('Email already exists');
            }

            // Create user
            $userId = $this->user->create([
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
                'roles' => ['ROLE_USER']
            ]);

            if (!$userId) {
                throw new \Exception('Failed to create user');
            }

            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $userId,
                    'username' => $data['username'],
                    'email' => $data['email']
                ]
            ]));
            return $response->withStatus(201)->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
    }

    public function logout(Request $request, Response $response): Response
    {
        // For JWT, we don't need to do anything on the server side
        // The client should remove the token
        $response->getBody()->write(json_encode([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
} 