<?php

namespace Controller;

class Genre extends ControllerAction
{
	public function details($request) {
            
		if (null === $genre = $this->getDataProvider()->getGenre($request->getParam('genre'))) {
		
			return false;
		}
		
		return array(
			'metaTitle' => $genre->name,
			'genre' => $genre,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/genres',
					'title' => 'Genres',
				),
				(object)array(
					'active' => true,
					'title' => $genre->name,
				),
			),
		);
	}
}
