<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function () {
    return Twig::create(__DIR__ . '/../templates', ['cache' => false]);
});

// Set Flash messages in Container
$container->set('flash', function () {
    return new \Slim\Flash\Messages();
});

// Create App
$app = AppFactory::create();

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));


// Define Main route
$app->get('/', function ($request, $response, $args) {
    $params = [
        'title' => 'Анализатор страниц',
        'menu' => [
            'main' => [
                'name' => 'Главная',
                'link' => '/',
                'active' => true
            ],
            'sites' => [
                'name' => 'Сайты',
                'link' => '/urls',
            ],
        ]
    ];

    return $this->get('view')->render($response, 'index.twig', $params);
})->setName('main');


// Run app
$app->run();
