<?php

namespace Controller;

use DataProvider;

class Releases extends ControllerAction
{
	public function latest($request) {
            
		return array(
			'metaTitle' => 'Latest Releases',
			'title' => 'Latest Releases',
			'releases' => DataProvider::getInstance()->getReleases('all'),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'All releases',
				),
			),
		);
	}
	
	public function upcomming($request) {
            
		return array(
			'metaTitle' => 'Upcomming Releases',
			'title' => 'Upcomming Releases',
			'releases' => DataProvider::getInstance()->getReleases('upcomming'),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Upcomming releases',
				),
			),
		);
	}
}
