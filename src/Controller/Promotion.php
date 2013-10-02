<?php

namespace Controller;

use DataProvider;

class Promotion extends ControllerAction
{
	public function view($request) {

		return array(
			'metaTitle' => 'Example Promotion',
			'title' => 'Example Promotion',
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Promotions',
				),
			),
		);
	}
}
