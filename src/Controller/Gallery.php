<?php

namespace Controller;

use DataProvider;

class Gallery extends ControllerAction
{
	public function index($request) {

		return array(
			'metaTitle' => 'Picture Gallery',
			'facebookImage' => 'http://www.3886records.de/img/gallery/n8lounge-entry.jpg',
			'galleryId' => $request->getParam('gallery'),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Gallery',
				),
			),
		);
	}
}
