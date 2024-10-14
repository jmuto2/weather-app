<?php

use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Http\Interfaces\ResponseInterface;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

require __DIR__ . '/../vendor/autoload.php';

// Instantiate the app
$app = AppFactory::create();
$app->addRoutingMiddleware();

$isProduction = strtolower(getenv('ENVIRONMENT')) === 'production';
$errorMiddleware = $app->addErrorMiddleware(!$isProduction, true, true);
// Set the Not Found Handler
$errorMiddleware->setErrorHandler(
    HttpNotFoundException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $responseFactory = new ResponseFactory();
        $streamFactory = new StreamFactory();
        $decoratedResponseFactory = new DecoratedResponseFactory($responseFactory, $streamFactory);
        return $decoratedResponseFactory->createResponse(404, '404 Not found');
    },
);

// Set the Not Allowed Handler
$errorMiddleware->setErrorHandler(
    HttpMethodNotAllowedException::class,
    function (ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails) {
        $responseFactory = new ResponseFactory();
        $streamFactory = new StreamFactory();
        $decoratedResponseFactory = new DecoratedResponseFactory($responseFactory, $streamFactory);
        return $decoratedResponseFactory->createResponse(405, '405 Not allowed');
    },
);

$app->get('/healthz', function (ServerRequestInterface $request, ResponseInterface $response) {
    return $response->withStatus(200);
});

$app->run();
