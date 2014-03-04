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

		return array(
			'metaTitle' => $artist->name,
			'facebookImage' => 'http://www.3886records.de/img/artists/'. $artist->key . '.jpg',
			'artist' => $artist,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/artists',
					'title' => 'Artists',
				),
				(object)array(
					'active' => true,
					'title' => $artist->name,
				),
			),
		);
	}

	public function booking($request) {
	
		if (null === $artist = $this->getDataProvider()->getArtist($request->getParam('artist'))) {
		
			return false;
		}
		
		$finished = false;
		$errors = array();
		if ($request->isPost()) {
			
			if (!$firstname = $request->getPost('firstname')) {
			
				$errors['firstname'] = 'Please enter your firstname.';
			}
			
			if (!$lastname = $request->getPost('lastname')) {
			
				$errors['lastname'] = 'Please enter your lastname.';
			}
			
			if (!$email = $request->getPost('email')) {
			
				$errors['email'] = 'Please enter your email address.';
			}			
			if (!$location = $request->getPost('location')) {
			
				$errors['location'] = 'Please enter the desired location.';
			}			
			if (!$location = $request->getPost('date')) {
			
				$errors['date'] = 'Please enter the desired date.';
			}	

			if (!$message = $request->getPost('message')) {
			
				$errors['message'] = 'Please add some information to let '. $artist->name .' know what you want.';
			}
			
			if (!count($errors)) {
			
				$finished = true;
			}
		}
		
		return array(
			'metaTitle' => $artist->name . ' Booking',
			'artist' => $artist,
			'errors' => $errors,
			'values' => $request->getPost(),
			'finished' => $finished,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/artists',
					'title' => 'Artists',
				),
				(object)array(
					'url' => '/artist/'. $artist->key,
					'title' => $artist->name,
				),(object)array(
					'active' => true,
					'title' => 'Booking',
				),
			),
		);
	}
}
