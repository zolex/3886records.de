<?php

namespace Controller;

use DataProvider;

class ControllerAction
{
	public function getDataProvider() {
	
		return DataProvider::getInstance();
	}

	public function requireLogin() {
	
		if (null === \Registry::get('user')) {

			header('Location: /login');
			exit;
		}
	}
}