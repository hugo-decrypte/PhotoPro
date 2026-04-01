<?php

namespace toubilib\api\actions;

use auth\src\api\dto\auth\CredentialsDTO;
use auth\src\api\providers\auth\AuthnProviderInterface;
use auth\src\api\providers\auth\JWTManager;
use auth\src\application_core\application\ports\spi\exceptions\DatabaseException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;

class PostAuthNewUserAction extends AbstractAction
{

    private AuthnProviderInterface $authnProvider;

    public function __construct(AuthnProviderInterface $authnProvider)
    {
        $this->authnProvider = $authnProvider;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $data = $rq->getParsedBody();

        // Validation des champs
        if (empty($data['email']) || empty($data['password'])) {
            throw new HttpBadRequestException($rq, "l'email et le mdp sont requis.");
        }

        $credentialDTO = new CredentialsDTO($data['email'], $data['password']);
        try {
            $this->authnProvider->register($credentialDTO);
        } catch (DatabaseException $e) {
            throw new HttpInternalServerErrorException($rq, "Erreur interne");
        }

        $authDTO = $this->authnProvider->signin($credentialDTO);
        // rep json
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