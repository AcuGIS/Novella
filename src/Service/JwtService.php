<?php

declare(strict_types=1);

namespace GeoLibre\Service;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JwtService
{
    private string $secret;
    private string $algorithm;
    private int $expiry;

    public function __construct(
        string $secret,
        string $algorithm = 'HS256',
        int $expiry = 3600 // 1 hour
    ) {
        $this->secret = $secret;
        $this->algorithm = $algorithm;
        $this->expiry = $expiry;
    }

    public function generateToken(array $payload): string
    {
        $issuedAt = time();
        $expire = $issuedAt + $this->expiry;

        $tokenPayload = array_merge($payload, [
            'iat' => $issuedAt,
            'exp' => $expire
        ]);

        return JWT::encode($tokenPayload, $this->secret, $this->algorithm);
    }

    public function validateToken(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->secret, $this->algorithm));
            return (array) $decoded;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function refreshToken(string $token): string
    {
        $payload = $this->validateToken($token);
        
        // Remove the old expiration and issued at
        unset($payload['exp'], $payload['iat']);
        
        // Generate a new token
        return $this->generateToken($payload);
    }
} 