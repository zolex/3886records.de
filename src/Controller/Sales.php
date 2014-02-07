<?php

namespace Controller;

use DataProvider;

class Sales extends ControllerAction
{
	public function index($request) {

		return array(
			'layout' => 'layout_plain',
		);
	}
}
