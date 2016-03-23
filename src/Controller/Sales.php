<?php

namespace Controller;

use DataProvider;

class Sales extends ControllerAction
{
	public function index($request) {

		if (@$_GET['hash'] === '5c7g4pg9s4') {

			$artistId = 46;

		} else if (@$_GET['hash'] === '7h40w09f8') {

			$artistId = 2;

		} else if (@$_GET['hash'] === '809nqc3f0z') {

			$artistId = 5;

		} else if (@$_GET['pass'] === 'ac39b4ptc0954') {
			 
			 $artistId = (integer)$_GET['artist'];

		} else die();

		
		
		$db = DataProvider::getInstance();

		$report = $db->getSalesReportByArtist($artistId);
		$details = $db->getNewSalesByArtist($artistId);

		return array(
			'layout' => 'layout_plain',
			'report' => $report,
			'details' => $details,
		);

	}
}
