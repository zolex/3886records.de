<?php

ini_set('error_reporting', -1);
ini_set('display_errors', true);

define('DS', DIRECTORY_SEPARATOR);

function __autoload($className) {

	$filename = 'src' . DS . implode(DS, explode('\\', $className)) . '.php';
	if (is_file($filename)) {
	
		include($filename);
	}
}

date_default_timezone_set('Europe/Berlin');

return array(

    'db' => (@include 'shared/database.php'),
	'smtp' => (@include 'shared/smtp.php'),
    'routes' => array(
        '^/$' => array(
            'template' => 'home_new',
			'controller' => array('Controller\Home', 'index')
        ),
        '^/home/?$' => array(
            'template' => 'home',
			'controller' => array('Controller\Home', 'index')
        ),
        '^/artists/?$' => array(
            'template' => 'artists',
            'controller' => array('Controller\Artist', 'overview')
        ),        
		'^/artist/(?P<artist>[^/]+)/?$' => array(
            'template' => 'artist',
            'controller' => array('Controller\Artist', 'details')
        ),
		'^/artist/(?P<artist>[^/]+)/booking?$' => array(
            'template' => 'booking',
            'controller' => array('Controller\Artist', 'booking')
        ),
        '^/events/?$' => array(
            'template' => 'events',
            'controller' => array('Controller\Events', 'overview')
        ), 
		'^/genre/(?P<genre>[^/]+)/?$' => array(
            'template' => 'genre',
            'controller' => array('Controller\Genre', 'details')
        ),
		'^/releases/latest/?$' => array(
            'template' => 'releases',
            'controller' => array('Controller\Releases', 'latest')
        ),
		'^/releases/upcoming/?$' => array(
            'template' => 'releases',
            'controller' => array('Controller\Releases', 'upcoming')
        ),
		'^/label/(?P<label>[^/]+)/?$' => array(
            'template' => 'label',
            'controller' => array('Controller\Labels', 'index')
        ),
        '^/contest/?$' => array(
            'template' => 'contest',
            'controller' => array('Controller\Contest', 'index')
        ),
		'^/subscriptions/?$' => array(
            'template' => 'subscriptions',
            'controller' => array('Controller\Subscription', 'subscribe')
        ),
		'^/subscriptions/confirm/(?P<email>[^/]+)/?$' => array(
            'template' => 'subscriptions',
            'controller' => array('Controller\Subscription', 'subscribe')
        ),
		'^/subscriptions/activate/(?P<token>[^/]+)/?$' => array(
            'template' => 'subscriptions',
            'controller' => array('Controller\Subscription', 'activate')
        ),
		'^/subscriptions/(?P<done>done)/?$' => array(
            'template' => 'subscriptions',
            'controller' => array('Controller\Subscription', 'activate')
        ),
		'^/subscriptions/manage/(?P<token>[^/]+)/?$' => array(
            'template' => 'subscriptions_manage',
            'controller' => array('Controller\Subscription', 'manage')
        ),
		'^/admin/?$' => array(
            'template' => 'admin_index',
            'controller' => array('Controller\Admin', 'index')
        ),
    ),
);
