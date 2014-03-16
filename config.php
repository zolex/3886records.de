<?php

define('DS', DIRECTORY_SEPARATOR);

function __autoload($className) {

	$filename = 'src' . DS . implode(DS, explode('\\', $className)) . '.php';
	if (is_file($filename)) {
	
		include($filename);
	}
}

date_default_timezone_set('Europe/Berlin');

return array(

    'debug' => true,
    'db' => (@include 'shared/database.php'),
	'smtp' => (@include 'shared/smtp.php'),
    'routes' => array(
        '^/$' => array(
            'template' =>  'home',
			'controller' => array('Controller\Home', 'index')
        ),
        '^/disclaimer/?$' => array(
            'template' => 'disclaimer',
			'controller' => array('Controller\Home', 'disclaimer')
        ),
		'^/home/?$' => array(
            'template' => 'home',
			'controller' => array('Controller\Home', 'index')
        ),
        '^/artists/?$' => array(
            'template' => 'artists',
            'controller' => array('Controller\Artist', 'overview')
        ),
        '^/djs/?$' => array(
            'template' => 'artists',
            'controller' => array('Controller\Artist', 'djs')
        ),        
		'^/artist/(?P<artist>[^/]+)/?$' => array(
            'template' => 'artist',
            'controller' => array('Controller\Artist', 'details')
        ),
        '^/dj/(?P<artist>[^/]+)/?$' => array(
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
		'^/subscriptions/(?P<done>done)/(?P<state>\d)/?$' => array(
            'template' => 'subscriptions',
            'controller' => array('Controller\Subscription', 'activate')
        ),
		'^/subscriptions/manage/(?P<token>[^/]+)/?$' => array(
            'template' => 'subscriptions_manage',
            'controller' => array('Controller\Subscription', 'manage')
        ),
        '^/promotion/(?P<key>[^/]+)/(?P<token>[^/]+)/?$' => array(
            'template' => 'promotion',
            'controller' => array('Controller\Promotion', 'view')
        ),
		'^/promotion/(?P<key>[^/]+)/(?P<token>[^/]+)/alt/?$' => array(
            'template' => 'webview',
            'controller' => array('Controller\Promotion', 'webview')
        ),
		'^/promotion/(?P<key>[^/]+)/(?P<token>[^/]+)/thanks?$' => array(
            'template' => 'promotion_thanks',
            'controller' => array('Controller\Promotion', 'thanks'),
        ),
	   '^/promotion/(?P<key>[^/]+)/(?P<token>[^/]+)/download?$' => array(
            'template' => null,
            'controller' => array('Controller\Promotion', 'download'),
        ),
		'^/admin/?$' => array(
            'template' => 'admin_index',
            'controller' => array('Controller\Admin', 'index')
        ),
		'^/event/(?P<event>[^/]+)/?$' => array(
            'template' => null,
            'controller' => array('Controller\Events', 'forward')
        ),
        '^/skitter/?$' => array(
            'template' => 'skitter',
            'controller' => array('Controller\Home', 'skitter')
        ),
        '^/psy-forge-gewinnspiel/?$' => array(
            'template' => 'win',
            'controller' => array('Controller\Home', 'win')
        ),
		'^/gewinnspiel/(?P<party>[^/]+)/?$' => array(
            'template' => 'sweepstake',
            'controller' => array('Controller\Home', 'sweepstake')
        ),
        '^/sales/?$' => array(
            'template' => 'sales_report',
            'controller' => array('Controller\Sales', 'index')
        ),
    ),
);
