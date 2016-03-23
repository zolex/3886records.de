<?php

namespace Controller;

use Request;

class Bookingmanager extends ControllerAction
{
	public function index($request) {
	
		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		$order = $request->get('order');
		if (!in_array($order, array('orgaName','personName', 'zipcode', 'lastContactTime'))) {

			$order = 'orgaName';
		}

		if ($order == 'zipcode') {

			$order = 'country,zipcode';
		
		} else if ($order == 'lastContactTime') {

			$order = 'lastContactTime';
		}

		$artists = array();
		if (!$user->rights & \Models\User::RIGHT_BOOKINGMANAGER) {

			header('Location: /profile');
			exit;
		}

		$stmt = $dbh->prepare('SELECT * FROM contacts c ORDER BY '. $order . ' ASC;');
		$stmt->execute();
		$contacts = array();
		while ($contact = $stmt->fetchObject()) {
		    
		    $contacts[] = $contact;
		}

		return array(
			'contacts' => $contacts,
			'metaTitle' => 'Booking manager',
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Booking Manager',
				),
			),
		);
	}

	public function getArtistDistanceByLocation($origin) {

		$cacheSeconds = 3600;
		$cachePath = '/srv/apache2/3886records.de/www/production/shared/cache';
		$key = 'AIzaSyCIST6cBlE59suAM6-QptxTMLh5Cl9VI6k';

		$dbh =  \Registry::get('dbh');
		$stmt = $dbh->prepare('SELECT id, is_topact, has_contract, name, zip, city, country FROM artists WHERE (zip IS NOT NULL AND zip != "") OR (city IS NOT NULL AND city != "") OR (country IS NOT NULL AND country != "");');
		$stmt->execute();
		$destinations = array();
		$artists = array();
		while ($artist = $stmt->fetchObject()) {
		    
		    $artists[] = $artist;
		    $destination = array();
			if (!empty($artist->zip)) $destination[] = $artist->zip;
			if (!empty($artist->city)) $destination[] = $artist->city;
			if (!empty($artist->country)) $destination[] = $artist->country;
		    $destinations[] = urlencode(implode(',', $destination));
		}

		$parameters = 'origins='. $origin .'&destinations='. implode('|', $destinations);
		$cacheFile = $cachePath .'/distance.'. md5($parameters) . '.json';
		if (!is_file($cacheFile) || time() > filemtime($cacheFile) + $cacheSeconds || empty($json = file_get_contents($cacheFile))) {

			$query = 'https://maps.googleapis.com/maps/api/distancematrix/json?key='. $key .'&'. $parameters;
			$json = file_get_contents($query);
			file_put_contents($cacheFile, $json);
		}

		$response = json_decode($json);


		foreach ($artists as $index => $artist) {

			$response->rows[0]->elements[$index]->id = $artist->id;
			$response->rows[0]->elements[$index]->name = $artist->name;
			$response->rows[0]->elements[$index]->is_topact = $artist->is_topact;
			$response->rows[0]->elements[$index]->has_contract = $artist->has_contract;
			$response->rows[0]->elements[$index]->original_location = urldecode($destinations[$index]);
			$response->rows[0]->elements[$index]->location = $response->destination_addresses[$index];
		}

		usort($response->rows[0]->elements, function($a, $b) {
		    return @$a->distance->value - @$b->distance->value;
		});

		return $response->rows[0]->elements;
	}

	public function artists($request) {

		$artists = $this->getArtistDistanceByLocation($request->get('origin'));

		return array(
			'metaTitle' => 'Artists Distances for Location',
			'artists' => $artists,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/bookingmanager',
					'title' => 'Bookingmanager',
				),
				(object)array(
					'active' => true,
					'title' => 'Location',
				),
			),
		);
	}

	public function edit($request) {
	
		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		if (!$user->rights & \Models\User::RIGHT_BOOKINGMANAGER) {

			header('Location: /profile');
			exit;
		}

		$contact = (object)array(
			'id' => null,
			'orgaName' => null,
			'lastContactTime' => null,
			'lastContactResult' => null,
			'orgaFacebook' => null,
			'personName' => null,
			'personFacebook' => null,
			'zipcode' => null,
			'country' => null,
			'goabase' => null,
			'phone' => null,
		);

		if ($contactId = (integer)$request->getParam('id')) {

			$stmt = $dbh->prepare('SELECT * FROM contacts WHERE id = :id');
			$stmt->bindValue('id', $contactId);
			$stmt->execute();
		
			$contact = $stmt->fetchObject();
		}

		$values = (array)$contact;
		$errors = array();
		if ($request->isPost()) {
	
	        $formData = $request->getPost();
        
	        if (empty($formData['orgaName'])) {

        		$errors['orgaName'] = 'Please enter an organization name.';
	        }

	        if (!empty($formData['orgaFacebook']) && !preg_match('#^https?://(www\.)?facebook\.[\w]{2,4}#', $formData['orgaFacebook'])) {

        		$errors['orgaFacebook'] = 'Please enter a valid facebook link.';
	        
	        } else if (!empty($formData['orgaFacebook'])) {

	        	$formData['orgaFacebook'] = preg_replace('/\?.+$/', '', $formData['orgaFacebook']);

	        	if (isset($contactId) && !empty($contactId)) {
	        	
	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE orgaFacebook = :facebook AND id != :id");
					$stmt->bindValue('id', $contactId);
	        	
	        	} else {

	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE orgaFacebook = :facebook");
	        	}

	        	$stmt->bindValue('facebook', $formData['orgaFacebook']);
				$stmt->execute();
				if ($existingContact = $stmt->fetchObject()) {

					$errors['orgaFacebook'] = 'A contact with this facebook link already exists! <a href="/bookingmanager/contact/edit/'. $existingContact->id .'">Edit it instead.</a>';
				}
	        }

	        if (!empty($formData['personFacebook']) && !preg_match('#^https?://(www\.)?facebook\.[\w]{2,4}#', $formData['personFacebook'])) {

        		$errors['personFacebook'] = 'Please enter a valid facebook link.';
	        
	        } else if (!empty($formData['personFacebook'])) {

	        	$formData['personFacebook'] = preg_replace('/\?.+$/', '', $formData['personFacebook']);

	        	if (isset($contactId) && !empty($contactId)) {
	        	
	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE personFacebook = :facebook AND id != :id");
					$stmt->bindValue('id', $contactId);
	        	
	        	} else {

	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE personFacebook = :facebook");
	        	}

	        	$stmt->bindValue('facebook', $formData['personFacebook']);
				$stmt->execute();
				if ($existingContact = $stmt->fetchObject()) {

					$errors['personFacebook'] = 'A contact with this facebook link already exists! <a href="/contact/edit/'. $existingContact->id .'">Edit it instead.</a>';
				}
	        }

	        if (!empty($formData['goabase']) && !preg_match('#^https?://(www\.)?goabase\.[\w]{2,4}#', $formData['goabase'])) {

        		$errors['goabase'] = 'Please enter a valid goabase link.';
	        
	        } else if (!empty($formData['goabase'])) {

	        	$formData['goabase'] = preg_replace('/\?.+$/', '', $formData['goabase']);

	        	if (isset($contactId) && !empty($contactId)) {
	        	
	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE goabase = :goabase AND id != :id");
					$stmt->bindValue('id', $contactId);
	        	
	        	} else {

	        		$stmt = $dbh->prepare("SELECT id FROM contacts WHERE goabase = :goabase");
	        	}

	        	$stmt->bindValue('goabase', $formData['goabase']);
				$stmt->execute();
				if ($existingContact = $stmt->fetchObject()) {

					$errors['goabase'] = 'A contact with this goabase link already exists! <a href="/contact/edit/'. $existingContact->id .'">Edit it instead.</a>';
				}
	        }

	        if (!empty($formData['lastContactTime']) && !preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $formData['lastContactTime'])) {

        		$errors['lastContactTime'] = 'Please enter a valid date and time.';
	        }

	        if (0 === count($errors)) {

	        	if (!$contact->id) {
	        	
	        		$stmt = $dbh->prepare('INSERT INTO contacts (lastContactTime, lastContactInfo, orgaName, orgaFacebook, personName, personFacebook, zipcode, country, goabase, phone, email) VALUES (:lastContactTime, :lastContactInfo, :orgaName, :orgaFacebook, :personName, :personFacebook, :zipcode, :country, :goabase, :phone, :email)');

	        	} else {

	        		$stmt = $dbh->prepare('UPDATE contacts SET `lastContactTime` = :lastContactTime, `lastContactInfo` = :lastContactInfo, `orgaName` = :orgaName, orgaFacebook = :orgaFacebook, personName = :personName, personFacebook = :personFacebook, zipcode = :zipcode, country = :country, goabase = :goabase, phone = :phone, email = :email WHERE id = :id LIMIT 1');
	        		$stmt->bindValue('id', $contact->id);
	        	}

	            $stmt->bindValue('orgaName', $formData['orgaName']);
	            $stmt->bindValue('orgaFacebook', $formData['orgaFacebook']);
                $stmt->bindValue('personName', $formData['personName']);
                $stmt->bindValue('personFacebook', $formData['personFacebook']);
                $stmt->bindValue('zipcode', $formData['zipcode']);
                $stmt->bindValue('country', $formData['country']);
                $stmt->bindValue('goabase', $formData['goabase']);
                $stmt->bindValue('phone', $formData['phone']);
                $stmt->bindValue('email', $formData['email']);
                $stmt->bindValue('lastContactTime', date('Y.m.d', strtotime($formData['lastContactTime'])));
                $stmt->bindValue('lastContactInfo', $formData['lastContactInfo']);

				if (!$stmt->execute()) {
		
					$errorInfo = $stmt->errorInfo();
					throw new \Exception($errorInfo[2], $errorInfo[1]);
				}

	            if (!$contact->id) {
	            
	            	$contactId = $dbh->lastInsertId();
	            	$contact->id = $contactId;
	            	$values['id'] = $contactId;
	            }

		        header('Location: /contact/edit/'. $contact->id . '?updated');
		        exit;
	        }
	    }

	    return array(
			'metaTitle' => 'Add Contact',
			'values' => $values,
			'errors' => $errors,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/bookingmanager',
					'title' => 'Bookingmanager',
				),
				(object)array(
					'active' => true,
					'title' => 'Add Contact',
				),
			),
		);
	}
}
