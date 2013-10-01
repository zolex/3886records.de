<?php

chdir(dirname(dirname(__FILE__)));
$config = require 'config.php';

$debug = true;
$request = new Request($config);

$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
DataProvider::getInstance()->setDbh($dbh);

$router = new Router();
list($template, $params) = $router->route($request);
if (!is_array($params)) {
	$params = array();
}

try {

    $content = ViewLoader::load($template, $params);
	
} catch (Exception $e) {

	if (false === $debug) {

		header("HTTP/1.0 404 Not Found");
		$content = ViewLoader::load('404', array('route' => $request->getRoute()));
		
	} else {
	
		throw $e;
	}
}

if ($request->isXmlHttpRequest()) {

    echo json_encode((object)array(

        'content' => $content,
        'params' => $params,    
    ));
    exit;
}

$dataProvider = DataProvider::getInstance();

$navigation = ViewLoader::load('navigation', array(
    'request' => $request,
	'artists' => $dataProvider->getArtists(),
	'genres' => $dataProvider->getGenres(),
	'labels' => $dataProvider->getLabels(),
));

echo ViewLoader::load('layout', array_merge($params, array(
    'navigation' => $navigation,
    'content' => $content
)));
