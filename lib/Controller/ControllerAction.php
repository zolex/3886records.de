<?php

namespace Controller;

use DataProvider;

class ControllerAction
{
	public function getDataProvider() {
	
		return DataProvider::getInstance();
	}
}