<?php

namespace photopro\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpUnauthorizedException;
use photopro\api\providers\auth\AuthnProviderInterface;
use photopro\api\providers\auth\JWTManager;

class PostAuthRefreshAction extends AbstractAction
{

    private AuthnProviderInterface $authnProvider;

    public function __construct(AuthnProviderInterface $authnProvider)
    {
        $this->authnProvider = $authnProvider;
    }


    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $authDTO = $rq->getAttribute('profile')?? throw new HttpUnauthorizedException($rq, "not authenticated");

        $authDTO = $this->authnProvider->refresh($authDTO);

        $payload = [
            'access_token' => $authDTO->getAccessToken(),
            'access_expires_in' => JWTManager::ACCESS_TOKEN_DURATION,
            'refresh_token' => $authDTO->getRefreshToken(),
            'refresh_expires_in' => JWTManager::REFRESH_TOKEN_DURATION,
            'user' => [
                'id' => $authDTO->getId(),
                'email' => $authDTO->getEmail(),
                'role' => $authDTO->getRole(),
            ]
        ];

        $rs->getBody()->write(json_encode($payload, JSON_UNESCAPED_UNICODE));
        return $rs
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json');
    }
}