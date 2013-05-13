<?php

ini_set('error_reporting', -1);
ini_set('display_errors', true);

require 'lib/ViewLoader.php';
require 'lib/Request.php';
require 'lib/Router.php';
require 'lib/Controller/Artist.php';

return array(

    'routes' => array(
        '^/$' => array(
            'template' => 'home',
        ),
        '^/home/?$' => array(
            'template' => 'home',
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
    ),
);
