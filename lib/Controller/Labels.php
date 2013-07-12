<?php

namespace Controller;

use DataProvider;

class Labels extends ControllerAction
{
	public function index($request) {
            
		if (null === $label = $this->getDataProvider()->getLabel($request->getParam('label'))) {
		
			return false;
		}

		return array(
			'metaTitle' => $label->name,
			'title' => $label->name,
			'label' => $label,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'title' => 'Labels',
				),
				(object)array(
					'title' => $label->name,
				),
			),
		);
	}
}
