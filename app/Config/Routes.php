<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Dashboard::index');


$routes->group('api', function($routes) {
    $routes->get('cryptos', 'CryptoController::getCryptos');
    $routes->get('tracked', 'CryptoController::getTrackedCryptos');
    $routes->post('track', 'CryptoController::trackCrypto');
    $routes->delete('untrack/(:num)', 'CryptoController::untrackCrypto/$1');
});

$routes->get('debug/api-key', 'DebugController::checkApiKey');
$routes->get('debug/session', 'CryptoController::debugSession');