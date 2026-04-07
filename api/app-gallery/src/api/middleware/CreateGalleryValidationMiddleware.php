<?php

namespace photopro\api\middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;

class CreateGalleryValidationMiddleware
{
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next): ResponseInterface
    {
        $data = (array) $request->getParsedBody();

        // Normalise le type en minuscule
        if (isset($data['type'])) {
            $data['type'] = strtolower($data['type']);
        }

        // Validation des champs obligatoires
        try {
            v::key('title', v::stringType()->notEmpty()->length(1, 255))
                ->key('type', v::stringType()->in(['public', 'private']))
                ->assert($data);
        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, 'Données invalides : ' . $e->getFullMessage(), $e);
        }

        // Si galerie privée → champs supplémentaires obligatoires
        if ($data['type'] === 'private') {
            try {
                v::key('access_code', v::stringType()->notEmpty()->length(4, 50))
                    ->key('client_email', v::email())
                    ->assert($data);
            } catch (NestedValidationException $e) {
                throw new HttpBadRequestException(
                    $request,
                    'Galerie privée : ' . $e->getFullMessage(),
                    $e
                );
            }
        }

        // Champs optionnels : on valide seulement s'ils sont présents
        if (isset($data['description']) && $data['description'] !== null) {
            try {
                v::stringType()->length(0, 5000)->assert($data['description']);
            } catch (NestedValidationException $e) {
                throw new HttpBadRequestException($request, 'Description invalide : ' . $e->getFullMessage(), $e);
            }
        }

        if (isset($data['cover_photo_id']) && $data['cover_photo_id'] !== null) {
            try {
                v::uuid()->assert($data['cover_photo_id']);
            } catch (NestedValidationException $e) {
                throw new HttpBadRequestException($request, 'cover_photo_id doit être un UUID valide', $e);
            }
        }

        // Réinjecte le body normalisé
        $request = $request->withParsedBody($data);

        return $next->handle($request);
    }
}