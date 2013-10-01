<?php

namespace Controller;

use DataProvider;

class Contest extends ControllerAction
{
	public function index($request) {

		return array(
			'metaTitle' => 'Psy&Prog.Contest',
		);
	}
}
