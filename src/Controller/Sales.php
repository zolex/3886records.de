<?php

namespace Controller;

use DataProvider;

class Sales extends ControllerAction
{
	public function index($request) {

		if (!isset($_GET['pass'])) die();
		
		$db = DataProvider::getInstance();
		if ($_GET['pass'] == 'ac39b4ptc0954') {

			return array(
				'layout' => 'layout_plain',
				'report' => $db->getSalesReportByArtist(1, 5),
				'details' => $db->getNewSalesByArtist(5),
			);
			
		} else if ($_GET['pass'] == '76gd99bdcf8ds') {

			return array(
				'layout' => 'layout_plain',
				'report' => $db->getSalesReportByArtist(1, 9),
				'details' => $db->getNewSalesByArtist(9),
			);
			
		} else if ($_GET['pass'] == 'sev9j88tw54') {
		
			
			return array(
				'layout' => 'layout_plain',
				'report' => $db->getSalesReportByArtist(1, $_GET['artist']),
				'details' => $db->getNewSalesByArtist($_GET['artist']),
			);
		}
	}
}
