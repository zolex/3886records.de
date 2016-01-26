<?php

namespace Controller;

use Request;

class Artist extends ControllerAction
{
	public function overview($request) {
	
		return array(
			'artists' => $this->getDataProvider()->getArtists(),
			'headline' => 'Artists Overview',
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Artists',
				),
			),
		);
	}

	public function djs($request) {
	
		return array(
			'artists' => $this->getDataProvider()->getDJs(),
			'headline' => 'DJs Overview',
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'DJs',
				),
			),
		);
	}
	
	public function details(Request $request) {
            
		if (null === $artist = $this->getDataProvider()->getArtist($request->getParam('artist'))) {
		
			return false;
		}

		if ($artist->type == 0) {

			$link = '/artists';
			$type = 'Artists';
		
		} else {

			$link = '/djs';
			$type = 'DJs';
		}

		return array(
			'metaTitle' => $artist->name,
			'metaTitleSuffix' => '3886records artist',
			'facebookImage' => 'http://www.3886records.de/img/artists/'. $artist->key . '.jpg',
			'artist' => $artist,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => $link,
					'title' => $type,
				),
				(object)array(
					'active' => true,
					'title' => $artist->name,
				),
			),
		);
	}
}
