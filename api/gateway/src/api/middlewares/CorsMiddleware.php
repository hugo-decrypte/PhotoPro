<?php
declare(strict_types=1);

namespace photopro\api\middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpUnauthorizedException;
use Slim\Psr7\Response;

class CorsMiddleware implements MiddlewareInterface
{
    private array $allowedOrigins;
    private array $allowedMethods;
    private array $allowedHeaders;
    private bool $allowCredentials;
    public function __construct(
        array $allowedOrigins = ['*'],
        array $allowedMethods = ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'],
        array $allowedHeaders = ['Content-Type', 'Authorization', 'X-Requested-With'],
        bool $allowCredentials = true,
    ) {
        $this->allowedOrigins = $allowedOrigins;
        $this->allowedMethods = $allowedMethods;
        $this->allowedHeaders = $allowedHeaders;
        $this->allowCredentials = $allowCredentials;
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        // Pas d'Origin = appel backend → on laisse passer
        if (!$request->hasHeader('Origin')) {
            return $handler->handle($request);
        }

        // Requête preflight
        if ($request->getMethod() === 'OPTIONS') {
            $response = new Response();
            return $this->addCorsHeaders($response, $request);
        }

        $response = $handler->handle($request);
        return $this->addCorsHeaders($response, $request);
    }

    private function addCorsHeaders(ResponseInterface $response, ServerRequestInterface $request): ResponseInterface {
        $origin = $request->getHeaderLine('Origin');

        // on détermine l'origine autorisée
        if (in_array('*', $this->allowedOrigins)) {
            $allowedOrigin = '*';
        } elseif (in_array($origin, $this->allowedOrigins)) {
            $allowedOrigin = $origin;
        } else {
            $allowedOrigin = $this->allowedOrigins[0] ?? '*';
        }

        // headers CORS
        $response = $response
            ->withHeader('Access-Control-Allow-Origin', $allowedOrigin)
            ->withHeader('Access-Control-Allow-Methods', implode(', ', $this->allowedMethods))
            ->withHeader('Access-Control-Allow-Headers', implode(', ', $this->allowedHeaders))
            ->withHeader('Access-Control-Max-Age', 3600);
        if ($this->allowCredentials && $allowedOrigin !== '*') {
            $response = $response->withHeader('Access-Control-Allow-Credentials', 'true');
        }

        // Exposer certains headers si nécessaire
        return $response->withHeader(
            'Access-Control-Expose-Headers',
            'Content-Length, Content-Type, Authorization'
        );
    }
}