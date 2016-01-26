<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

if (true === $config['debug']) {

	ini_set('error_reporting', -1);
	ini_set('display_errors', 1);
}

$request = new Request($config);
$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
DataProvider::getInstance()->setDbh($dbh);
Registry::set('dbh', $dbh);

// cutstomer session
session_start();
$user = null;
if (isset($_SESSION['csid'])) {

	$dhb = Registry::get('dbh');
	$stmt = $dbh->prepare('SELECT id, artist_id, name, rights, cstime FROM users WHERE csid = :csid LIMIT 1');
	$stmt->bindValue('csid', $_SESSION['csid']);
	$stmt->execute();
	if ($user = $stmt->fetchObject()) {

		if (strtotime($user->cstime) + 600 < time()) {

			$stmt = $dbh->prepare('UPDATE users SET csid = NULL, cstime = NULL WHERE csid = :csid LIMIT 1');
			$stmt->bindValue('csid', $_SESSION['csid']);
			$stmt->execute();

			$_SESSION['csid'] = null;
			unset($_SESSION['csid']);
			header('Location: /');
			exit;
		}

		$stmt = $dbh->prepare('UPDATE users SET cstime = NOW() WHERE csid = :csid LIMIT 1');
		$stmt->bindValue('csid', $_SESSION['csid']);
		$stmt->execute();

		\Registry::set('user', $user);
	}
}

$router = new Router();
list($template, $params) = $router->route($request);
if (!is_array($params)) {
	$params = array();
}

try {

	$content = ViewLoader::load($template, $params);
	
} catch (\Exception $e) {

	header("HTTP/1.0 404 Not Found");
	$content = ViewLoader::load('404', array(
		'route' => $request->getRoute(),
		'exception' => $e,
		'displayException' => $config['debug'],
	));
}

if ($request->isXmlHttpRequest()) {

    echo json_encode((object)array(

        'content' => $content,
        'params' => $params,    
    ));
    exit;
}

if (isset($params['noLayout']) && true === $params['noLayout']) {

	echo $content;
	exit;
}

$layout = 'layout';
if (isset($params['layout'])) {

	$layout = $params['layout'];
}

$dataProvider = DataProvider::getInstance();

$navigation = ViewLoader::load('navigation', array(
    'request' => $request,
	'artists' => $dataProvider->getArtists(),
	'djs' => $dataProvider->getDJs(),
	'genres' => $dataProvider->getGenres(),
	'labels' => $dataProvider->getLabels(),
	'user' => $user,
));


echo ViewLoader::load($layout, array_merge($params, array(
    'navigation' => $navigation,
    'content' => $content
)));