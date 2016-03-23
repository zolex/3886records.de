<?php

namespace Controller;

use DataProvider;

class Contest extends ControllerAction
{
	public function index($request) {

		return array(
			'metaTitle' => 'Psypek - Early Morning Remix Contest',
			'facebookImage' => 'https://www.3886records.de/img/releases/early-morning-remix-contest.jpg',
			'facebookDescription' => 'Take the chance to get your remix published on all major stores!',
		);
	}
}
