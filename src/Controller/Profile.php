<?php

namespace Controller;

class Profile extends ControllerAction
{
	public function index($request) {

		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		$stmt = $dbh->prepare('SELECT * FROM artists WHERE id = :artist_id');
		$stmt->bindValue('artist_id', $user->artist_id);
		$stmt->execute();
		
		$dataProvider = $this->getDataProvider();
		
		if ($artist = $stmt->fetchObject()) {
		
		
			$stmt = $dbh->prepare('SELECT g.* FROM artist_genres ag INNER JOIN genres g ON g.id = ag.genre_id WHERE ag.artist_id = :artist_id');
			$stmt->bindValue('artist_id', $user->artist_id);
			$stmt->execute();
			$artist->genres = array();
			while ($genre = $stmt->fetchObject()) {
			    
			    $artist->genres[] = $genre;
			}
			
			return array(
				'artist' => $artist,
			    'events' => $dataProvider->getEventsByArtist($artist->id),
			    'releases' => $dataProvider->getReleasesByArtist($artist->id),
			    'links' => $dataProvider->getLinksByArtist($artist->id),
				'breadcrumb' => array(
					(object)array(
						'url' => '/',
						'title' => 'Home',
					),
					(object)array(
						'active' => true,
						'title' => 'Profile',
					),
				),
			);

		} else {

			return array(
				'template' => 'profile_user',
			    'events' => $dataProvider->getEvents(\Models\Event::UPCOMMING),
				'user' => $user,
				'breadcrumb' => array(
					(object)array(
						'url' => '/',
						'title' => 'Home',
					),
					(object)array(
						'active' => true,
						'title' => 'Profile',
					),
				),
			);
		}
	}

	public function signupcodes($request) {

		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		$artists = array();
		if ($user->rights & \Models\User::RIGHT_SIGNUPCODES) {

			srand();
			$stmt = $dbh->prepare('SELECT * FROM artists WHERE visible = 1 ORDER BY name');
			$stmt->execute();
			while ($artist = $stmt->fetchObject()) {

				$stmt2 = $dbh->prepare('SELECT * FROM signup_codes WHERE artist_id = :artist_id');
				$stmt2->bindValue('artist_id', $artist->id);
				$stmt2->execute();

				if ($code = $stmt2->fetchObject()) {

					$artist->code = $code->code;
					$artist->codeUsed = (boolean)$code->used_by_id;

				} else {

					$code = substr(md5(uniqid(rand())), 0, 8);
					$artist->code = $code;
					$artist->codeUsed = false;

					$stmt3 = $dbh->prepare('INSERT INTO signup_codes (artist_id, code) VALUES(:artist_id, :code)');
					$stmt3->bindValue('artist_id', $artist->id);
					$stmt3->bindValue('code', $code);
					$stmt3->execute();
				}

				$artists[] = $artist;
			}
		}

		return array(
			'artists' => $artists,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => '/profile',
					'title' => 'Profile',
				),
				(object)array(
					'active' => true,
					'title' => 'Signup Codes',
				),
			),
		);
	}

	public function logout($request) {

		$dbh = \Registry::get('dbh');
		$stmt = $dbh->prepare('UPDATE users SET csid = NULL, cstime = NULL WHERE csid = :csid LIMIT 1');
		$stmt->bindValue('csid', $_SESSION['csid']);
		$stmt->execute();

		$_SESSION['csid'] = null;
		unset($_SESSION['csid']);
		header('Location: /');
		exit;
	}

	public function login($request) {

		if (null !== \Registry::get('user')) {

			header('Location: /profile');
			exit;
		}

		$loginFailed = false;
		if ($request->isPost()) {

			$formData = $request->getPost();
			$dbh = \Registry::get('dbh');

			$stmt = $dbh->prepare('SELECT salt FROM users WHERE name = :name LIMIT 1');
			$stmt->bindValue('name', $formData['user']);
			$stmt->execute();
			if ($user = $stmt->fetchObject()) {

				$pass = new \Password($formData['password'], $user->salt);
				$stmt = $dbh->prepare('SELECT id FROM users WHERE name = :name AND password = :pass LIMIT 1');
				$stmt->bindValue('name', $formData['user']);
				$stmt->bindValue('pass', (string)$pass);
				$stmt->execute();
				if ($user = $stmt->fetchObject()) {

					srand();
					$csid = hash('sha512', uniqid(rand().$user->id));
					$stmt = $dbh->prepare('UPDATE users SET csid = :csid, cstime = NOW() WHERE id = :id LIMIT 1');
					$stmt->bindValue('csid', $csid);
					$stmt->bindValue('id', $user->id);
					$stmt->execute();

					$_SESSION['csid'] = $csid;

					$location = '/profile';
					if (isset($_SESSION['post_login_redirect']) && !empty($_SESSION['post_login_redirect'])) {

						$location = $_SESSION['post_login_redirect'];
						$_SESSION['post_login_redirect'] = null;
					}

					header('Location: '. $location);
					exit;
				}
			}

			$loginFailed = true;
		}

		return array(
			'template' => 'login',
			'reg' => $request->get('reg'),
			'loginFailed' => $loginFailed,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/profile',
					'title' => 'Profile',
				),
				(object)array(
					'active' => true,
					'title' => 'Login',
				),
			),
		);
	}

	public function signup($request) {
	
		if (null !== \Registry::get('user')) {

			header('Location: /profile');
			exit;
		}

		$dbh = \Registry::get('dbh');

		$errors = array();
		$formData = array();
		$isArtist = true;
		$artistId = @(integer)$_SESSION['signup_artist_id'];
		if ($newAartistId = (integer)base64_decode($request->get('ref'))) {

			$artistId = $newAartistId;
			$_SESSION['signup_artist_id'] = $newAartistId;
		}

		if (0 === $artistId) {

			$artistId = null;
			$isArtist = false;
			$code = $request->get('code');
			if ($code) {

				$_SESSION['signup_code'] = $code;

			} else {

				$code = $_SESSION['signup_code'];
			}

			$stmt = $dbh->prepare('SELECT * FROM signup_codes WHERE code = :code LIMIT 1');
			$stmt->bindValue('code', $code);
			$stmt->execute();
			if (!$signupCode = $stmt->fetchObject()) {

				header('Location: /');
				exit;
			}

			$formData['code'] = $code;
		}

		if ($request->isPost()) {

			$formData = $request->getPost();

			if ($isArtist) {
			
				$stmt = $dbh->prepare('SELECT * FROM signup_codes WHERE code = :code AND artist_id = :artist_id AND used_by_id IS NULL LIMIT 1');
				$stmt->bindValue('artist_id', $artistId);

			} else {

				$stmt = $dbh->prepare('SELECT * FROM signup_codes WHERE code = :code LIMIT 1');
			}

			$formData['code'] = trim($formData['code']);
			$stmt->bindValue('code', $formData['code']);
			$stmt->execute();
			if (!$signupCode = $stmt->fetchObject()) {

				$errors['code'] = 'Invalid signup code or artist already registered.';
			}

			$formData['user'] = trim($formData['user']);
			if (empty($formData['user'])) {

				$errors['user'] = 'Please enter a username';
			
			} else {

				$stmt = $dbh->prepare('SELECT * FROM users WHERE name = :user LIMIT 1');
				$stmt->bindValue('user', $formData['user']);
				$stmt->execute();
				if ($existingUser = $stmt->fetchObject()) {

					$errors['user'] = 'Username already taken. Please chose another one.';
				}
			}

			if (empty($formData['password'])) {

				$errors['password'] = 'Please enter a password';
			}

			if ($formData['password'] !== $formData['password_conf']) {

				$errors['password_conf'] = 'Password confirmation does not match.';
			}

			if (0 === count($errors)) {

				$pass = new \Password($formData['password']);

				$stmt = $dbh->prepare('INSERT INTO users (artist_id, name, password, salt, created_at) VALUES (:artist_id, :name, :pass, :salt, NOW())');
				$stmt->bindValue('artist_id', $artistId);
				$stmt->bindValue('name', $formData['user']);
				$stmt->bindValue('pass', (string)$pass);
				$stmt->bindValue('salt', $pass->getSalt());
				$stmt->execute();
				$userId = $dbh->lastInsertId();

				if ($isArtist) {

					$stmt = $dbh->prepare('UPDATE signup_codes SET used_by_id = :user_id WHERE code = :code LIMIT 1');
					$stmt->bindValue('user_id', $userId);
					$stmt->bindValue('code', $formData['code']);
					$stmt->execute();
				
				} else {

					$stmt = $dbh->prepare('DELETE FROM signup_codes WHERE code = :code LIMIT 1');
					$stmt->bindValue('code', $formData['code']);
					$stmt->execute();
				}

				header('Location: /login?reg=1');
				exit;
			}
		}

		unset($formData['password']);
		unset($formData['password_conf']);

		if (!isset($formData['user']) || empty($formData['user'])) {

			$stmt = $dbh->prepare('SELECT * FROM artists WHERE id = :artist_id');
			$stmt->bindValue('artist_id', $artistId);
			$stmt->execute();
			if ($artist = $stmt->fetchObject()) {

				$formData['user'] = strtolower(preg_replace('/[^\w]/', '', $artist->name));
			}
		}

		return array(
			'template' => 'signup',
			'errors' => $errors,
			'values' => $formData,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'url' => '/profile',
					'title' => 'Profile',
				),
				(object)array(
					'active' => true,
					'title' => 'Signup',
				),
			),
		);
	}
	
	public function edit($request) {
	
	    $this->requireLogin();
	    $dbh = \Registry::get('dbh');
	    $user = \Registry::get('user');
	    
	    $stmt = $dbh->prepare('SELECT * FROM artists WHERE id = :id LIMIT 1');
	    $stmt->bindValue('id', $user->artist_id);
	    $stmt->execute();
	    $artist = $stmt->fetchObject();
	    
	    $stmt = $dbh->prepare('SELECT g.* FROM artist_genres ag INNER JOIN genres g ON g.id = ag.genre_id WHERE ag.artist_id = :artist_id');
	    $stmt->bindValue('artist_id', $user->artist_id);
	    $stmt->execute();
	    $artist->genres = array();
	    while ($genre = $stmt->fetchObject()) {
	    
	        $artist->genres[] = $genre->id;
	    }
	    
	    $stmt = $dbh->prepare('SELECT * FROM genres ORDER BY name');
	    $stmt->execute();
	    $genres = array();
	    while ($genre = $stmt->fetchObject()) {
	        
	        $genres[] = $genre;
	    }

	    $stmt = $dbh->prepare('SELECT * FROM artist_links WHERE artist_id = :artist_id');
	    $stmt->bindValue('artist_id', $user->artist_id);
	    $stmt->execute();
	    while ($link = $stmt->fetchObject()) {
	    
	        $artist->{'link_'. $link->type} = $link->link;
	    }

	    $errors = array();
	    $formData = (array)$artist;
	    	
	    if ($request->isPost()) {
	
	        $formData = $request->getPost();
	        
	        if (!empty($formData['link_facebook'])) {

	        	if (!preg_match('/^https?:\/\/.+/', $formData['link_facebook'])) {

	        		$errors['link_facebook'] = 'Please enter a valid URL';
	        	}
	        }

	        if (!empty($formData['link_soundcloud'])) {

	        	if (!preg_match('/^https?:\/\/.+/', $formData['link_soundcloud'])) {

	        		$errors['link_soundcloud'] = 'Please enter a valid URL';
	        	}
	        }

	        if (!empty($formData['link_mixcloud'])) {

	        	if (!preg_match('/^https?:\/\/.+/', $formData['link_mixcloud'])) {

	        		$errors['link_mixcloud'] = 'Please enter a valid URL';
	        	}
	        }

	        $artist->genres = $formData['genres'] ? array_keys($formData['genres']) : array();
	        if (!count($formData['genres'])) {
	            
	            $errors['genres'] = 'Please choose at least one genre.';
	        }
	        
	        if (!empty($formData['email']) && !preg_match('/^[^@]+@[^@]+\.[^@]{2,5}$/', $formData['email'])) {
	            
	            $errors['email'] = 'Please enter a valid e-mail address.';
	        }
	        
	        if (!empty($formData['password'])) {
	            
                $pass = new \Password($formData['password_current'], $user->salt);
                $stmt = $dbh->prepare('SELECT id FROM users WHERE id = :id AND password = :pass LIMIT 1');
                $stmt->bindValue('id', $user->id);
                $stmt->bindValue('pass', (string)$pass);
                $stmt->execute();
                if (!$stmt->fetchObject()) {
                    
                    $errors['password_current'] = 'Invalid password.';
                } 
    	        
    	        if (empty($formData['password'])) {
    	
    	            $errors['password'] = 'Please enter a password';
    	        }
    	
    	        if ($formData['password'] !== $formData['password_conf']) {
    	
    	            $errors['password_conf'] = 'Password confirmation does not match.';
    	        }
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
	        }

	        if (isset($formData['payment_type']) && !empty($formData['payment_type'])) {

	        	if ($formData['payment_type'] == 'paypal') {

	        		if (!preg_match('/^[^@]+@[^@]+\.[^@]{2,5}$/', $formData['payment_paypal'])) {

	        			$errors['payment_paypal'] = 'Please enter a valid paypal address.';
	        		}
	        	
	        	} else if ($formData['payment_type'] == 'sepa') {


	        		if (empty($formData['payment_name'])) {

	        			$errors['payment_name'] = 'Please enter an account name.';
	        		}

	        		if (empty($formData['payment_iban'])) {

	        			$errors['payment_iban'] = 'Please enter an IBAN.';
	        		}

	        		if (empty($formData['payment_bic'])) {

	        			$errors['payment_bic'] = 'Please enter a BIC.';
	        		}

	        	}
	        }

	        if (!empty($formData['fee_dj_desired']) && !(integer)$formData['fee_dj_desired']) {

	        	$errors['fee_dj_desired'] = 'Please enter a valid fee.';
	        }

	        if (!empty($formData['fee_dj_min']) && !(integer)$formData['fee_dj_min']) {

	        	$errors['fee_dj_min'] = 'Please enter a valid fee.';
	        }

	        if (!empty($formData['fee_live_desired']) && !(integer)$formData['fee_live_desired']) {

	        	$errors['fee_live_desired'] = 'Please enter a valid fee.';
	        }

	        if (!empty($formData['fee_live_min']) && !(integer)$formData['fee_live_min']) {

	        	$errors['fee_live_min'] = 'Please enter a valid fee.';
	        }
	
	        if (0 === count($errors)) {
	
	            if (!empty($formData['password'])) {
	            
	                $pass = new \Password($formData['password']);
    	            $stmt = $dbh->prepare('UPDATE users SET password = :password, salt = :salt WHERE id = :id LIMIT 1');
    	            $stmt->bindValue('password', (string)$pass);
    	            $stmt->bindValue('salt', $pass->getSalt());
    	            $stmt->bindValue('id', $user->id);
    	            $stmt->execute();
	            }

	            $stmt = $dbh->prepare('UPDATE artists SET firstname = :firstname, lastname = :lastname, phone = :phone, fbprofile = :fbprofile, fee_dj_desired = :fee_dj_desired, fee_dj_min = :fee_dj_min, fee_live_desired = :fee_live_desired, fee_live_min = :fee_live_min, payment_type = :payment_type, payment_paypal = :payment_paypal, payment_name = :payment_name, payment_iban = :payment_iban, payment_bic = :payment_bic, email = :email, street = :street, zip = :zip, city = :city, country = :country, shortInfo = :shortInfo, longInfo = :longInfo WHERE id = :id LIMIT 1');
	            $stmt->bindValue('firstname', $formData['firstname']);
                $stmt->bindValue('lastname', $formData['lastname']);
                $stmt->bindValue('shortInfo', $formData['shortInfo']);
                $stmt->bindValue('longInfo', $formData['longInfo']);
				$stmt->bindValue('email', $formData['email']);
				$stmt->bindValue('phone', $formData['phone']);
				$stmt->bindValue('fbprofile', $formData['fbprofile']);
				$stmt->bindValue('street', $formData['street']);
				$stmt->bindValue('zip', $formData['zip']);
				$stmt->bindValue('city', $formData['city']);
				$stmt->bindValue('country', $formData['country']);
				$stmt->bindValue('payment_type', $formData['payment_type']);
				$stmt->bindValue('payment_paypal', $formData['payment_paypal']);
				$stmt->bindValue('payment_name', $formData['payment_name']);
				$stmt->bindValue('payment_iban', $formData['payment_iban']);
				$stmt->bindValue('payment_bic', $formData['payment_bic']);
				$stmt->bindValue('fee_dj_desired', $formData['fee_dj_desired']);
				$stmt->bindValue('fee_dj_min', $formData['fee_dj_min']);
				$stmt->bindValue('fee_live_desired', $formData['fee_live_desired']);
				$stmt->bindValue('fee_live_min', $formData['fee_live_min']);
                $stmt->bindValue('id', $user->artist_id);
	            $stmt->execute();
	            
	            
	            $stmt = $dbh->prepare('DELETE FROM artist_genres WHERE artist_id = :artist_id');
	            $stmt->bindValue('artist_id', $user->artist_id);
	            $stmt->execute();
	            
	            foreach ($formData['genres'] as $genreId => $on) {
	             
	                $stmt = $dbh->prepare('INSERT INTO artist_genres (artist_id, genre_id) VALUES(:artist_id, :genre_id)');
	                $stmt->bindValue('artist_id', $artist->id);
	                $stmt->bindValue('genre_id', $genreId);
	                $stmt->execute();
	            }

	            $stmt = $dbh->prepare('DELETE FROM artist_links WHERE artist_id = :artist_id');
	            $stmt->bindValue('artist_id', $user->artist_id);
	            $stmt->execute();

	            if (!empty($formData['link_facebook'])) {

	            	$stmt = $dbh->prepare('INSERT INTO artist_links (artist_id, link, type) VALUES(:artist_id, :link, "facebook")');
	            	$stmt->bindValue('artist_id', $user->artist_id);
	            	$stmt->bindValue('link', $formData['link_facebook']);
	            	$stmt->execute();
		        }

		        if (!empty($formData['link_soundcloud'])) {

		        	$stmt = $dbh->prepare('INSERT INTO artist_links (artist_id, link, type) VALUES(:artist_id, :link, "soundcloud")');
	            	$stmt->bindValue('artist_id', $user->artist_id);
	            	$stmt->bindValue('link', $formData['link_soundcloud']);
	            	$stmt->execute();
		        }

		        if (!empty($formData['link_mixcloud'])) {

		        	$stmt = $dbh->prepare('INSERT INTO artist_links (artist_id, link, type) VALUES(:artist_id, :link, "mixcloud")');
	            	$stmt->bindValue('artist_id', $user->artist_id);
	            	$stmt->bindValue('link', $formData['link_mixcloud']);
	            	$stmt->execute();
		        }
	            
	            if (isset($_FILES['image']) && !empty($_FILES['image']['tmp_name'])) {

		            $basePath = '/srv/apache2/3886records.de/www/production/shared/public/img/artists/';
		            $imagePath = $basePath . $artist->key .'_original.jpg';
		            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
		            $img = imagecreatefromjpeg($imagePath);
		            $resized = imagecreatetruecolor(200, 200);
		            imagecopyresampled($resized, $img, 0, 0, 0, 0, 200, 200, $imgWidth, $imgHeight);
		            imagefilter($resized, IMG_FILTER_GRAYSCALE);
		            imagejpeg($resized, $basePath . $artist->key .'.jpg', 100);
		        }
					            
		       	$mailData = $formData;
		       	unset($mailData['password_current']);
		       	unset($mailData['password']);
		       	unset($mailData['password_conf']);
		       	unset($mailData['MAX_FILE_SIZE']);
				ob_start();
				print_r($mailData);
				$body = ob_get_clean();

				$config = \Registry::get('config');
				$transport = \Swift_SmtpTransport::newInstance($config['smtp']['host'], $config['smtp']['port'])
					->setUsername($config['smtp']['user'])
					->setPassword($config['smtp']['password']);

				$mailer = \Swift_Mailer::newInstance($transport);
	            $message = \Swift_Message::newInstance();
				$message->setSubject($artist->name . " updated his profile")
					->setFrom(array('noreply@3886records.de' => '3886records'))
					->setTo(array("spekta@3886records.de"))
					->setBody($body, 'text/plain');

				$mailer->send($message);

	            header('Location: /profile/edit?upd=1');
	            exit;
	        }
	    }
	
		unset($formData['password']);
	    unset($formData['password']);
	    unset($formData['password_conf']);
	
	    return array(
	        'updated' => (boolean)$request->get('upd'),
	        'artist' => $artist,
	        'genres' => $genres,
            'errors' => $errors,
            'values' => $formData,
            'breadcrumb' => array(
                (object)array(
                    'url' => '/',
                    'title' => 'Home',
                ),
                (object)array(
                    'url' => '/profile',
                    'title' => 'Profile',
                ),
                (object)array(
                    'active' => true,
                    'title' => 'Signup',
                ),
            ),
	    );
	}
}
