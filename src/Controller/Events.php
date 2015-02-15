<?php

namespace Controller;

use Request;

class Events extends ControllerAction
{
	public function overview($request) {
	
		$facebookImage = null;
		if ($event = $this->getDataProvider()->getNextEvent()) {
		
			$facebookImage = 'http://www.3886records.de/img/events/'. $event->flyer;
		}

		return array(
			'metaTitle' => 'Upcomming Events',
			'facebookImage' => $facebookImage,
			'facebookDescription' => 'Show all the upcomming events with Thirtyeight Eightysix Records artists and DJs.',
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

	public function json($request) {
	
		$events = $this->getDataProvider()->getEvents(\Models\Event::UPCOMMING, true);
		echo '{"events": ['. implode(',', $events) . ']}';
		exit;
	}
	
	public function forward($request) {
	
		$event = $request->getParam('event');
		$dp = $this->getDataProvider();
		$dp->trackQrCodeVisit($event);
		
		switch ($event) {
		
			case 'spaceopera':
				header('Location: http://www.facebook.com/events/534877699920790/?fref=flyer');
				exit;
				
			case 'stampfn8en':
				header('Location: https://www.facebook.com/events/164675243725736/?fref=flyer');
				exit;
				
			case 'stampfn8en-cologne':
				header('Location: https://www.facebook.com/events/347252045411886/?ref=flyer');
				exit;
				
			default:
				header('Location: /');
				exit;
		}
	}
}
