<?php

namespace Controller;

use Request;

class Events extends ControllerAction
{
	public function overview($request) {
	
		$facebookImage = null;
		if ($event = $this->getDataProvider()->getNextEvent()) {
		
			$facebookImage = 'http://www.3886records.de/img/events/'. $event->flyer;
		}

		return array(
			'user' => \Registry::get('user'),
			'metaTitle' => 'Upcomming Events',
			'facebookImage' => $facebookImage,
			'facebookDescription' => 'Show all the upcomming events with Thirtyeight Eightysix Records artists and DJs.',
			'events' => (object)array(
				'upcomming' => $this->getDataProvider()->getEvents(\Models\Event::UPCOMMING, true),
				'past' => $this->getDataProvider()->getEvents(\Models\Event::PAST, true),
			),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Events',
				),
			),
		);
	}

	public function json($request) {
	
		$events = $this->getDataProvider()->getEvents(\Models\Event::UPCOMMING, true);
		echo '{"events": ['. implode(',', $events) . ']}';
		exit;
	}
	
	public function forward($request) {
	
		$event = $request->getParam('event');
		$dp = $this->getDataProvider();
		$dp->trackQrCodeVisit($event);
		
		switch ($event) {
		
			case 'spaceopera':
				header('Location: http://www.facebook.com/events/534877699920790/?fref=flyer');
				exit;
				
			case 'stampfn8en':
				header('Location: https://www.facebook.com/events/164675243725736/?fref=flyer');
				exit;
				
			case 'stampfn8en-cologne':
				header('Location: https://www.facebook.com/events/347252045411886/?ref=flyer');
				exit;
				
			default:
				header('Location: /');
				exit;
		}
	}

	public function edit($request) {
	
		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		$event = new \Models\Event;
		if ($eventId = (integer)$request->getParam('id')) {

			$stmt = $dbh->prepare('SELECT * FROM events WHERE id = :event_id');
			$stmt->bindValue('event_id', $eventId);
			$stmt->execute();
		
			$eventData = $stmt->fetchObject();
			$event->fromObject($eventData);

			$event->fromTime = date('d.m.Y H:i', strtotime($event->fromTime));
			$event->toTime = date('d.m.Y H:i', strtotime($event->toTime));

			$stmt = $dbh->prepare('SELECT artist_id AS id FROM event_artists WHERE event_id = :event_id');
			$stmt->bindValue('event_id', $eventId);
			$stmt->execute();

			$event->artists = array();
			while ($row = $stmt->fetchObject()) {

				$event->artists[] = $row->id;
			}
		}

		$values = (array)$event->toObject();
		$errors = array();
		if ($request->isPost()) {
	
	        $formData = $request->getPost();
	        if (isset($formData['artists']) && is_array($formData['artists'])) {
	        
	        	$formData['artists'] = array_keys($formData['artists']);
	        }

	        $values = $formData;
	        
			if (isset($formData['artists']) && !count($formData['artists'])) {

				$errors['artists'] = 'Please select at least one artist.';
			}

	        if (empty($formData['name'])) {

        		$errors['name'] = 'Please enter an event name.';
	        }

	        if (empty($formData['shortInfo'])) {

        		$errors['shortInfo'] = 'Please enter an location name.';
	        }

	        if (!preg_match('/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/', $formData['fromTime'])) {

        		$errors['fromTime'] = 'Please enter a valid date and time.';
	        }

	        if (!preg_match('/^\d{2}\.\d{2}\.\d{4} \d{2}:\d{2}$/', $formData['toTime'])) {

        		$errors['toTime'] = 'Please enter a valid date and time.';
	        }

	        if (empty($formData['facebook']) || !preg_match('#^https?://(www\.)?facebook\.[\w]{2,4}/events/\d+#', $formData['facebook'])) {

        		$errors['facebook'] = 'Please enter a valid facebook link.';
	        }

	        if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {
	             
	            if (exif_imagetype($_FILES['image']['tmp_name']) != IMAGETYPE_JPEG || imagecreatefromjpeg($_FILES['image']['tmp_name']) === false) {
	                 
	                $errors['image'] = 'Only JPEG images allowed.';
	            }
	             
	            list($imgWidth, $imgHeight) = getimagesize($_FILES['image']['tmp_name']);
	            if ($imgWidth !== $imgHeight) {
	                 
	                $errors['image'] = 'Image must be quadratic.';
	            }
	             
	            if (isset($errors['image'])) {
	                 
	                unlink($_FILES['image']['tmp_name']);
	            }

	        } else if (!$event->id) {

	        	$errors['image'] = 'Please upload an image.';
	        }




	        if (0 === count($errors)) {

	        	if (!$event->id) {
	        	
	        		$stmt = $dbh->prepare('INSERT INTO events (`key`, name, shortInfo, fromTime, toTime, facebook, visible) VALUES (:key, :name, :shortInfo, :fromTime, :toTime, :facebook, 1)');

	        	} else {

	        		$stmt = $dbh->prepare('UPDATE events SET `key` = :key, name = :name, shortInfo = :shortInfo, fromTime = :fromTime, toTime = :toTime, facebook = :facebook WHERE id = :id LIMIT 1');
	        		$stmt->bindValue('id', $event->id);
	        	}

	            $stmt->bindValue('name', $formData['name']);
	            $stmt->bindValue('key', strtolower(preg_replace('/[^\w\d]+/', '', $formData['name'])));
                $stmt->bindValue('shortInfo', $formData['shortInfo']);
                $stmt->bindValue('fromTime', date('Y.m.d H:i:s', strtotime($formData['fromTime'])));
                $stmt->bindValue('toTime', date('Y.m.d H:i:s', strtotime($formData['toTime'])));
				$stmt->bindValue('facebook', $formData['facebook']);
				if (!$stmt->execute()) {
		
					$errorInfo = $stmt->errorInfo();
					throw new \Exception($errorInfo[2], $errorInfo[1]);
				}

	            if (!$event->id) {
	            
	            	$eventId = $dbh->lastInsertId();
	            	$event->id = $eventId;
	            	$values['id'] = $eventId;
	            }

	            $stmt = $dbh->prepare('DELETE FROM event_artists WHERE event_id = :event_id');
				$stmt->bindValue('event_id', $event->id);
				if (!$stmt->execute()) {
		
					$errorInfo = $stmt->errorInfo();
					throw new \Exception($errorInfo[2], $errorInfo[1]);
				}

				foreach ($values['artists'] as $artistId) {

					$stmt = $dbh->prepare('INSERT INTO event_artists (event_id, artist_id) VALUES(:event_id, :artist_id)');
					$stmt->bindValue('artist_id', $artistId);
					$stmt->bindValue('event_id', $event->id);
					if (!$stmt->execute()) {
		
						$errorInfo = $stmt->errorInfo();
						throw new \Exception($errorInfo[2], $errorInfo[1]);
					}
				}


	        	if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {

		            $basePath = '/srv/apache2/3886records.de/www/production/shared/public/img/events/';
		            $imagePath = $basePath . $event->id .'_original.jpg';
		            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
		            $img = imagecreatefromjpeg($imagePath);
		            $resized = imagecreatetruecolor(200, 200);
		            imagecopyresampled($resized, $img, 0, 0, 0, 0, 200, 200, $imgWidth, $imgHeight);
		            //imagefilter($resized, IMG_FILTER_GRAYSCALE);
		            imagejpeg($resized, $basePath . $event->id .'.jpg', 100);
		            unlink($imagePath);

		            $stmt = $dbh->prepare('UPDATE events SET flyer = :flyer WHERE id = :id LIMIT 1');
	        		$stmt->bindValue('id', $event->id);
	        		$stmt->bindValue('flyer', $event->id .'.jpg');

	        		if (!$stmt->execute()) {
		
						$errorInfo = $stmt->errorInfo();
						throw new \Exception($errorInfo[2], $errorInfo[1]);
					}
		        }

		        header('Location: /event/edit/'. $event->id);
		        exit;
	        }
	    }

	    if (!is_array($values['artists'])) $values['artists'] = array();

		return array(
			'metaTitle' => 'Add Event',
			'facebookTitle' => 'Add Event',
			'artists' => $this->getDataProvider()->getActs(),
			'values' => $values,
			'errors' => $errors,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/events',
					'title' => 'Events',
				),
				(object)array(
					'active' => true,
					'title' => 'Add',
				),
			),
		);
	}
}
