<?php

namespace Controller;

use DataProvider;

class Home extends ControllerAction
{
	public function index($request) {
            
		$dp = DataProvider::getInstance();

		return array(
			'releases' => array_slice($dp->getReleases('upcomming'), 0, 5),
			'latestReleases' => array_slice($dp->getReleases('all'), 0, 5),
			'events' => array_slice($dp->getEvents(), 0, 5),
		);
	}
	
	public function disclaimer($request) {
            
		return array(
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Disclaimer',
				),
			),
		);
	}
}
