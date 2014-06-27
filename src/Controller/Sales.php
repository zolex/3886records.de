<?php

namespace Controller;

use DataProvider;

class Sales extends ControllerAction
{
	public function index($request) {

		if (!isset($_GET['pass']) || $_GET['pass'] !== 'ac39b4ptc0954') die();

		$db = DataProvider::getInstance();

		return array(
			'layout' => 'layout_plain',
			'report' => $db->getSalesReportByLabel(2),
			'details' => $db->getNewSales(2),
		);
	}
}
