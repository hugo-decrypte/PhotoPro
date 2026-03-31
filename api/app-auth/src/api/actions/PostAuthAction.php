<?php

namespace toubilib\api\actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpUnauthorizedException;
use toubilib\api\dto\auth\CredentialsDTO;
use toubilib\api\providers\auth\AuthnProviderInterface;
use toubilib\api\providers\auth\JWTManager;

class PostAuthAction extends AbstractAction
{
    protected AuthnProviderInterface $providerAuth;

    public function __construct(AuthnProviderInterface $providerAuth)
    {
        $this->providerAuth = $providerAuth;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        try {
            $data = $rq->getParsedBody();

            // Validation des champs
            if (empty($data['email']) || empty($data['password'])) {
                throw new HttpBadRequestException($rq, "l'email et le mdp sont requis.");
            }

            // Création du DTO des credentials
            $credentials = new CredentialsDTO(
                email: $data['email'],
                password: $data['password']
            );

            // Appel du provider pourgénérer les tokens
            $authDTO = $this->providerAuth->signin($credentials);

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


            //je sais pas si c'est bien codé mais normalement ça marche
        } catch (HttpBadRequestException $e) {
            throw $e;
        } catch (\RuntimeException $e) {
            //erreur d'auth ou credentials non valides
            throw new HttpUnauthorizedException($rq, $e->getMessage());
        } catch (\Exception $e) {
            // erreurs type serveur
            throw new HttpInternalServerErrorException($rq, $e->getMessage());
        }
    }
}
