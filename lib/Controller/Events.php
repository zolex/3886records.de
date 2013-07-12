<?php

namespace Controller;

use Request;

class Events extends ControllerAction
{
	public function overview($request) {
	
		return array(
			'events' => (object)array(
				'upcomming' => $this->getDataProvider()->getEvents(1, null, true),
				'past' => $this->getDataProvider()->getEvents(2, null, true),
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
