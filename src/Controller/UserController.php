<?php

namespace GeoLibre\Controller;

use GeoLibre\Model\User;
use GeoLibre\Service\JwtService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UserController
{
    private JwtService $jwtService;

    public function __construct(JwtService $jwtService)
    {
        $this->jwtService = $jwtService;
    }

    public function register(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Email and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $user = new User();
            $user->setEmail($data['email']);
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
            $user->save();
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $user->getId(),
                    'email' => $user->getEmail()
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

    public function login(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        
        // Validate input
        if (empty($data['email']) || empty($data['password'])) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Email and password are required'
            ]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        
        try {
            $user = User::findByEmail($data['email']);
            if (!$user || !password_verify($data['password'], $user->getPassword())) {
                throw new \Exception('Invalid credentials');
            }

            $token = $this->jwtService->generateToken([
                'id' => $user->getId(),
                'email' => $user->getEmail()
            ]);
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => [
                    'token' => $token
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

    public function getProfile(Request $request, Response $response): Response
    {
        try {
            $user = $request->getAttribute('user');
            
            $response->getBody()->write(json_encode([
                'status' => 'success',
                'data' => [
                    'id' => $user['id'],
                    'email' => $user['email']
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

    public function refreshToken(Request $request, Response $response): Response
    {
        try {
            $authorization = $request->getHeaderLine('Authorization');
            if (preg_match('/Bearer\s+(.*)$/i', $authorization, $matches)) {
                $token = $matches[1];
                $newToken = $this->jwtService->refreshToken($token);
                
                $response->getBody()->write(json_encode([
                    'status' => 'success',
                    'data' => [
                        'token' => $newToken
                    ]
                ]));
                return $response->withHeader('Content-Type', 'application/json');
            }
            
            throw new \Exception('Invalid authorization header');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]));
            return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
} 