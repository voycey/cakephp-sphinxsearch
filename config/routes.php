<?php
use Cake\Routing\Router;

Router::plugin('Sphinx', function ($routes) {
    $routes->fallbacks('DashedRoute');
});
