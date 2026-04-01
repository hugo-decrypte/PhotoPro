<?php

namespace toubilib\api\providers\auth;

use Firebase\JWT\ExpiredException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

class JWTManager
{

    const ACCESS_TOKEN_DURATION = 900;
    const REFRESH_TOKEN_DURATION = 3600;

    private string $secret;

    public function __construct()
    {
        $this->secret = $_ENV['JWT_SECRET'];
    }


    public function createAccessToken(array $payload): string
    {
        return JWT::encode([ 'iss'=>'http://auth.myapp.net',
            'aud'=>'http://api.myapp.net',
            'iat'=>time(),
            'exp'=>time() + self::ACCESS_TOKEN_DURATION,
            'sub' => $payload['id'],
            'data' => $payload
        ], $this->secret, 'HS512');
    }

    public function createRefreshToken(array $payload): string
    {
        return JWT::encode([ 'iss'=>'http://auth.myapp.net',
            'aud'=>'http://api.myapp.net',
            'iat'=>time(),
            'exp'=>time() + self::REFRESH_TOKEN_DURATION,
            'sub' => $payload['id'],
            'data' => $payload
        ], $this->secret, 'HS512');
    }

    /**
     * @throws InvalidJWTTokenException
     */
    public function decodeToken(string $token): array
    {
        try {
            $tokenArray = JWT::decode($token, new Key($this->secret, 'HS512'));
        } catch (ExpiredException $e) {
            throw new InvalidJWTTokenException("Token expiré");
        } catch (SignatureInvalidException | \UnexpectedValueException | \DomainException $e) {
            throw new InvalidJWTTokenException("Token invalide");
        }
        return (array) $tokenArray->data;
    }
}