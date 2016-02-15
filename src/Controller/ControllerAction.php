<?php

namespace Controller;

use DataProvider;

class ControllerAction
{
	public function getDataProvider() {
	
		return DataProvider::getInstance();
	}

	public function requireLogin() {
	
		$request = \Registry::get('request');
		if (null === \Registry::get('user')) {

			$_SESSION['post_login_redirect'] = $_SERVER['REQUEST_URI'];

			header('Location: /login');
			exit;
		}
	}
}