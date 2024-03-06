<?php

use DI\Container;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Slim\Flash\Messages;
use App\Url\Url;
use App\Database\UrlManager;
use App\Database\UrlCheckManager;
use App\Database\Connection;
use Dotenv\Dotenv;
use App\Parser;
use App\UrlCheck;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\ClientException;

require __DIR__ . '/../vendor/autoload.php';

// Create Container
$container = new Container();
AppFactory::setContainer($container);

// Set view in Container
$container->set('view', function () {
    return Twig::create(__DIR__ . '/../templates', [
        'cache' => false,
        //'debug' => true
    ]);
});

// Set Flash messages in Container
$container->set('flash', function () {
    return new Messages();
});

// Create App
$app = AppFactory::create();

// Add Twig-View Middleware
$app->add(TwigMiddleware::createFromContainer($app));


$app->addErrorMiddleware(true, true, true);

// Get Router
$router = $app->getRouteCollector()->getRouteParser();

session_start();

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->safeLoad();

// For Templates
$params = [
    'title' => 'Анализатор страниц',
    'menu' => [
        'main' => [
            'name' => 'main',
            'link' => '/',
            'desc' => 'Главная',
        ],
        'sites' => [
            'name' => 'sites',
            'link' => '/urls',
            'desc' => 'Сайты',
        ],
    ],
    'menu_active' => 'main'
];


$connection = new Connection();
$UrlManager = new UrlManager($connection);
$UrlCheckManager = new UrlCheckManager($connection);


$app->get('/', function ($request, $response, $args) use ($params) {
    return $this->get('view')->render($response, 'index.twig', $params);
})->setName('main');


$app->post('/urls', function ($request, $response) use ($params, $router, $UrlManager) {
    $Url = new Url($request->getParsedBodyParam('url')['name']);
    if (!$Url->isValid()) {
        return $this->get('view')->render($response, 'index.twig', array_merge($params, [
            'error' => $Url->getError(),
            'url' => $Url->getName(),
            'menu_active' => 'sites'
        ]))->withStatus(422);
    }

    $UrlManager->save($Url);

    if ($Url->isNew()) {
        $this->get('flash')->addMessage('success', 'Страница успешно добавлена');
    } else {
        $this->get('flash')->addMessage('warning', 'Страница уже существует ');
    }
    return $response->withRedirect($router->urlFor('url', ['id' => $Url->getId()]));
});

//Check url
$app->post('/urls/{url_id}/checks', callable: function ($request, $response, $args)
 use ($params, $router, $UrlManager, $UrlCheckManager) {

    $url_id = $args['url_id'];
    $Url = $UrlManager->getUrlById($url_id);

    if (!$Url) {
        return $this->get('view')->render($response, 'index.twig', array_merge($params, [
            'menu_active' => 'main'
        ]))->withStatus(500);
    }

    try {
        $client = new GuzzleHttp\Client();
        $raw_response = $client->request('GET', $Url->getName());
        $this->get('flash')->addMessage('success', 'Страница успешно проверена');
    } catch (ClientException $e) {
        $raw_response = $e->getResponse();
        $this->get('flash')->addMessage('warning', 'Проверка была выполнена успешно, но сервер ответил с ошибкой');
    } catch (ConnectException $e) {
        $this->get('flash')->addMessage('danger', 'Произошла ошибка при проверке, не удалось подключиться');
        return $response->withRedirect($router->urlFor('url', ['id' => $Url->getId()]));
    }

    $parser = new Parser($raw_response, $Url);
    $UrlCheck = $parser->parseResponse();
    $UrlCheckManager->save($UrlCheck);

    return $response->withRedirect($router->urlFor('url', ['id' => $Url->getId()]));
});


//One url
$app->get('/urls/{id}', function ($request, $response, $args) use ($params, $UrlManager, $router) {
    $messages = $this->get('flash')->getMessages();
    $Url = $UrlManager->getUrlById((int)$args['id']);
    if (is_null($Url) || ((int)$args['id'] != $args['id'])) {
        return $response->withRedirect($router->urlFor('404'));
    }
    $params = array_merge($params, [
        'menu_active' => '',
        'messages' => $messages,
        'url' => $Url->toArray(),
        'checks' => $UrlManager->getChecksByUrl($Url)
    ]);
    return $this->get('view')->render($response, 'one_url.twig', $params);
})->setName('url');


//Table of urls
$app->get('/urls', function ($request, $response) use ($params, $UrlManager) {
    $sites = $UrlManager->getAllUrls();
    $sites = [
        'sites' => $sites
    ];
    return $this->get('view')->render($response, 'urls.twig', array_merge($params, [
        'menu_active' => 'sites'
    ], $sites));
})->setName('urls');

//404
$app->get('/404', function ($request, $response) use ($params) {
    $params = array_merge($params, [
        'menu_active' => '',
    ]);
    return $this->get('view')->render($response, 'errors/404.twig', $params);
})->setName('404');

$app->get('/{page}', function ($request, $response) use ($router) {
    return $response->withRedirect($router->urlFor('404'));
});

// Run app
$app->run();
