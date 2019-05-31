<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/functions.php';

function main($args = []) {
    date_default_timezone_set('Europe/London');

    \Sinevia\Serverless::openwhisk($args);

    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    $uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

    $extension = pathinfo($uri, PATHINFO_EXTENSION);

    /*  1. Static files */
    $staticFileExtensions = ['css', 'js'];

    if (in_array($extension, $staticFileExtensions)) {
        $name = trim($uri, '/');
        $file = basePath('public/' . $name);
        if (file_exists($file)) {
            $response = \Sinevia\Template::fromFile($file);
            return responseHtml($response);
        }
        return responseHtml($name . ' DOES NOT exist on this server');
    }

    /* 2. Define routes */
    $router = new Phroute\Phroute\RouteCollector();

    $router->filter('ApiVerifyUser', function() {
        //$o = new App\Controllers\Api\BaseController();
        //$response = $o->verifyUserRequest();
        //return $response;
    });

    $router->group(array('prefix' => '/api'), function(Phroute\Phroute\RouteCollector $router) {
        
    });

    $router->group(array('prefix' => '/user'), function(Phroute\Phroute\RouteCollector $router) {
        $router->controller('/', 'App\Controllers\User\HomeController');
    });

    $router->group(array('prefix' => '/'), function(Phroute\Phroute\RouteCollector $router) {
        $router->controller('/', 'App\Controllers\Guest\HomeController');
    });

    try {
        $dispatcher = new Phroute\Phroute\Dispatcher($router->getData());
        $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $uri);
    } catch (\Phroute\Phroute\Exception\HttpRouteNotFoundException $e) {
        $response = $e->getMessage();
    } catch (\Phroute\Phroute\Exception\HttpMethodNotAllowedException $e) {
        $response = 'Method not allowed';
    } catch (\Exception $e) {
        $response = '<pre>' . $e->getMessage() . "\n" . $e->getTraceAsString() . '</pre>';
    }

    return responseHtml($response);
}

function responseHtml($html) {
    return ['body' => $html];
}
