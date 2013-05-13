<?php

chdir(dirname(dirname(__FILE__)));
$config = require 'config.php';

$request = new Request();
$router = new Router($config['routes']);
list($template,$params) = $router->route($request);

try {
    $content = ViewLoader::load($template, $params);
} catch (Exception $e) {
    header("HTTP/1.0 404 Not Found");
    $content = ViewLoader::load('404', array('route' => $request->getRoute()));
}

if ($request->isXmlHttpRequest()) {

    echo json_encode((object)array(

        'content' => $content,
        'params' => $params,    
    ));
    exit;
}

$navigation = ViewLoader::load('navigation', array(
    'request' => $request,
));

echo ViewLoader::load('layout', array_merge($params, array(
    'navigation' => $navigation,
    'content' => $content
)));
