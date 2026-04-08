<?php

namespace photopro\api\actions;

use photopro\api\providers\auth\AuthnException;
use photopro\api\providers\auth\AuthnProviderInterface;
use photopro\core\application\usecases\auth\AuthnServiceInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Exception\HttpUnauthorizedException;

class PatchPasswordAction
{
    public function __construct(
        private AuthnProviderInterface $jwtProvider,
        private AuthnServiceInterface $authnService,
    ) {
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $auth = $request->getHeaderLine('Authorization');
        $token = preg_replace('/^Bearer\s+/i', '', trim($auth));

        if ($token === '') {
            throw new HttpUnauthorizedException($request, 'Token manquant');
        }

        try {
            $authUser = $this->jwtProvider->getSignedInUser($token);
        } catch (AuthnException $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }

        $data = $request->getParsedBody();
        if (!is_array($data)) {
            $data = [];
        }

        $current = isset($data['current_password']) ? (string) $data['current_password'] : '';
        $new = isset($data['new_password']) ? (string) $data['new_password'] : '';

        if ($current === '' || $new === '') {
            throw new HttpBadRequestException($request, 'current_password et new_password sont requis.');
        }

        try {
            $this->authnService->changePassword($authUser->getId(), $current, $new);
        } catch (\InvalidArgumentException $e) {
            throw new HttpBadRequestException($request, $e->getMessage());
        } catch (\RuntimeException $e) {
            throw new HttpUnauthorizedException($request, $e->getMessage());
        }

        $response->getBody()->write(json_encode([
            'success' => true,
            'message' => 'Mot de passe mis à jour.',
        ], JSON_UNESCAPED_UNICODE));

        return $response
            ->withStatus(200)
            ->withHeader('Content-Type', 'application/json; charset=utf-8');
    }
}
