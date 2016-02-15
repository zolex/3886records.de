<?php

namespace Controller;

use DataProvider;

class Sales extends ControllerAction
{
	public function index($request) {

		if ($_GET['pass'] !== 'ac39b4ptc0954') die();
		
		$db = DataProvider::getInstance();

		$artistId = (integer)$_GET['artist'];

		$report = $db->getSalesReportByArtist(1, $artistId);
		$details = $db->getNewSalesByArtist($artistId);

		return array(
			'layout' => 'layout_plain',
			'report' => $report,
			'details' => $details,
		);

	}
}
