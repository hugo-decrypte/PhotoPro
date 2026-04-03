<?php

namespace photopro\api\actions;

use Exception;
use photopro\core\application\ports\api\ServiceGalleryInterface;
use photopro\core\application\ports\spi\exceptions\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpInternalServerErrorException;
use Slim\Exception\HttpNotFoundException;

class AddCommentAction
{
    public function __construct(
        private ServiceGalleryInterface $serviceGallery
    ) {}

//    public function __invoke(Request $request, Response $response, array $args): Response
//    {
//        try {
//            $galleryId = $args['id'];
//            $photoId = $args['photoId'];
//            $data = (array) $request->getParsedBody();
//
//            $comment = $this->serviceGallery->addComment($galleryId, $photoId, $data);
//
//            $response->getBody()->write(json_encode([
//                'success' => true,
//                'data' => $comment
//            ]));
//
//            return $response
//                ->withHeader('Content-Type', 'application/json')
//                ->withStatus(201);
//
//        } catch (\InvalidArgumentException $e) {
//            $response->getBody()->write(json_encode([
//                'success' => false,
//                'error' => $e->getMessage()
//            ]));
//
//            return $response
//                ->withHeader('Content-Type', 'application/json')
//                ->withStatus(400);
//
//        } catch (\RuntimeException $e) {
//            $status = $e->getCode() ?: 400;
//
//            $response->getBody()->write(json_encode([
//                'success' => false,
//                'error' => $e->getMessage()
//            ]));
//
//            return $response
//                ->withHeader('Content-Type', 'application/json')
//                ->withStatus($status);
//        }
//    }
    public function __invoke(Request $request, Response $response): Response {
        $comment_dto = $request->getAttribute('comment_dto') ?? null;
        if(is_null($comment_dto)) {
            throw new Exception("Erreur récupération DTO de création d'un commentaire");
        }
        try {
            $this->serviceGallery->addComment($comment_dto);
            return $response->withStatus(201);
        } catch (EntityNotFoundException $e) {
            throw new HttpNotFoundException($request, $e->getMessage());
        } catch (Exception $e) {
            throw new HttpInternalServerErrorException($request, $e->getMessage());
        }
    }
}