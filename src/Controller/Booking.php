<?php

namespace Controller;

use Request;

class Booking extends ControllerAction
{
	public function index($request) {
	
		/*
		if (null === $artist = $this->getDataProvider()->getArtist($request->getParam('artist'))) {
		
			return false;
		}
		*/
		
		$finished = false;
		$errors = array();
		if ($request->isPost()) {
			
			if (!$artist = $request->getPost('artist')) {
			
				$errors['artist'] = 'Please choose an artist.';
			}
			
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
			
			if (!$location = $request->getPost('size')) {
			
				$errors['size'] = 'Please enter the desired event size.';
			}

			if (!$message = $request->getPost('message')) {
			
				$errors['message'] = 'Please add some information to let us know what you want.';
			}
			
			if (!count($errors)) {
			
				$finished = true;
				
				$config = $request->getConfig('smtp');
				$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
				  ->setUsername($config['user'])
				  ->setPassword($config['password']);

				  
				$booking = $request->getPost();
				$booking['artist'] = $this->getDataProvider()->getArtistById($booking['artist']);
				  
				$mailer = \Swift_Mailer::newInstance($transport);
				$message = \Swift_Message::newInstance();
				$body = \ViewLoader::load('email/booking_request', array(
					'booking' => (object)$booking,
				));

				$message->setSubject('3886records Booking Request')
				  ->setFrom(array('noreply@3886records.de' => '3886records'))
				  ->setTo(array('spekta@3886records.de'))
				  ->setBody($body, 'text/html');

				$mailer->send($message);
			}
		}
		
		$artists = $this->getDataProvider()->getArtists();
		usort($artists, function($a, $b) {
			
			return $a->name > $b->name;
		});
		
		$djs = $this->getDataProvider()->getDJs();
		usort($djs, function($a, $b) {
			
			return $a->name > $b->name;
		});
		
		$values = $request->getPost();
		if ((!isset($values['artist']) || empty($values['artist'])) && $request->get('artist')) {
		
			$values['artist'] = $request->get('artist');
		}
		
		
		return array(
			'metaTitle' => 'Bookings',
			'artists' => $artists,
			'djs' => $djs,
			'errors' => $errors,
			'values' => $values,
			'finished' => $finished,
			'facebookImage' => 'http://www.3886records.de/img/bookings.jpg',
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Booking',
				),
			),
		);
	}
}
