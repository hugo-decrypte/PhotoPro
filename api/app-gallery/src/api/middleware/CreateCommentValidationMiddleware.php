<?php

namespace photopro\api\middleware;

use DateTime;
use photopro\core\application\ports\api\InputCommentDTO;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;

class CreateCommentValidationMiddleware {
    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $next) : ResponseInterface {
        $route_params = RouteContext::fromRequest($request)
            ->getRoute()
            ->getArguments() ?? null;

        $data = $request->getParsedBody();
        $data["galleryId"] = $route_params["id"];
        $data["photoId"] = $route_params["photoId"];

        try {
            v::key('authorName', v::stringType()->notEmpty())
                ->key('content', v::stringType()->notEmpty())
                ->key('createdAt', v::stringType()->notEmpty())
            ->assert($data);

        } catch (NestedValidationException $e) {
            throw new HttpBadRequestException($request, "Invalid data: " . $e->getFullMessage(), $e);
        }

        //vérification format des datetime
        foreach (['createdAt'] as $datetime) {
            $data[$datetime] = urldecode($data[$datetime]);
            $date = DateTime::createFromFormat('Y-m-d H:i:s', $data[$datetime]);
            if (!$date || $date->format('Y-m-d H:i:s') !== $data[$datetime]) {
                throw new HttpBadRequestException($request, "Le champ $datetime doit etre au format Y-m-d H:i:s");
            }
        }

        $DTO = new InputCommentDTO($data);
        $request = $request->withAttribute('comment_dto', $DTO);

        return $next->handle($request);
    }
}