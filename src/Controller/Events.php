<?php

namespace Controller;

use Request;

class Events extends ControllerAction
{
	public function overview($request) {
	
		return array(
			'events' => (object)array(
				'upcomming' => $this->getDataProvider()->getEvents(\Models\Event::UPCOMMING, true),
				'past' => $this->getDataProvider()->getEvents(\Models\Event::PAST, true),
			),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Events',
				),
			),
		);
	}
}
