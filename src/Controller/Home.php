<?php

namespace Controller;

use DataProvider;

class Home extends ControllerAction
{
	public function index($request) {
            
		$dp = DataProvider::getInstance();

		return array(
			'releases' => array_slice($dp->getReleases('upcomming'), 0, 5),
			'latestReleases' => array_slice($dp->getReleases('all'), 0, 3),
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

	public function skitter($request) {

		$dp = DataProvider::getInstance();

		return array(
			'releases' => array_slice($dp->getReleases('upcomming'), 0, 5),
			'latestReleases' => array_slice($dp->getReleases('all'), 0, 5),
			'events' => array_slice($dp->getEvents(), 0, 5),
		);
	}

	public function win($request) {

		$dp = DataProvider::getInstance();

		if ($request->isPost()) {

			$dp->insertSweepstake($_POST);
			echo json_encode(array('message' => 'Danke für Deine Teilnahme!'));
			die();
		}

		return array(
			'metaTitle' => "Gewinne freien Eintritt für die Psy-Forge / Spekta's Birthday",
			'facebookDescription' => "Für den 24. Januar in der Klangstation gibt es 5 Mal freien Eintritt zu gewinnen! Erlebe internationale Top Acts wie Necmi, Nitro & Glycerine, Nayana She, Spectral Vibration, Spekta, Maskay und HighQ.",
			'facebookImage' => "http://www.3886records.de/img/psy-forge.jpg",
		);
	}
	
	public function sweepstake($request) {

		$dp = DataProvider::getInstance();

		if ($request->isPost()) {

			$dp->insertSweepstake($_POST);
			echo json_encode(array('message' => 'Danke für Deine Teilnahme!'));
			die();
		}

		$sweepstake = null;
		if ($party = $request->getParam('party')) {
		
			$sweepstake = $dp->getSweepstake($party);
		
		} else {
		
			$sweepstake = $dp->getSweepstake('psy-forge');
		}
		
		if (!$sweepstake) {
		
			// not existant
		}
		
		return array(
			'metaTitle' => $sweepstake->info,
			'metaDescription' => $sweepstake->description,
			'facebookDescription' => $sweepstake->description,
			'facebookImage' => $sweepstake->image,
			'sweepstake' => $sweepstake,
		);
	}
}
