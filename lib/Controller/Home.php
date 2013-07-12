<?php

namespace Controller;

use DataProvider;

class Home extends ControllerAction
{
	public function index($request) {
            
		return array(
			'releases' => array_slice(DataProvider::getInstance()->getReleases('upcomming'), 0, 5),
			'events' => array_slice(DataProvider::getInstance()->getEvents(), 0, 5),
		);
	}
}
