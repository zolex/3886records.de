<?php

namespace Controller;

use Request;

class Profile extends ControllerAction
{
	public function index($request) {

		$this->requireLogin();
		$user = \Registry::get('user');
		$dbh =  \Registry::get('dbh');

		$artists = array();
		if ($user->rights == 1) {

			srand();
			$stmt = $dbh->prepare('SELECT * FROM artists WHERE visible = 1 ORDER BY name');
			$stmt->execute();
			while ($artist = $stmt->fetchObject()) {

				$stmt2 = $dbh->prepare('SELECT * FROM signup_codes WHERE artist_id = :artist_id');
				$stmt2->bindValue('artist_id', $artist->id);
				$stmt2->execute();

				if ($code = $stmt2->fetchObject()) {

					$artist->code = $code->code;

				} else {

					$code = substr(md5(uniqid(rand())), 0, 8);
					$artist->code = $code;

					$stmt3 = $dbh->prepare('INSERT INTO signup_codes (artist_id, code) VALUES(:artist_id, :code)');
					$stmt3->bindValue('artist_id', $artist->id);
					$stmt3->bindValue('code', $code);
					$stmt3->execute();
				}

				$artists[] = $artist;
			}
		}

		$stmt = $dbh->prepare('SELECT * FROM artists WHERE id = :artist_id');
		$stmt->bindValue('artist_id', $user->artist_id);
		$stmt->execute();
		$artist = $stmt->fetchObject();

		return array(
			'artist' => $artist,
			'artists' => $artists,
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
					header('Location: /profile');
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

		$artistId = (integer)$_SESSION['signup_artist_id'];
		if ($newAartistId = (integer)base64_decode($request->get('ref'))) {

			$artistId = $newAartistId;
			$_SESSION['signup_artist_id'] = $newAartistId;
		}

		if (0 === $artistId) {

			header('Location: /');
			exit;
		}

		if ($request->isPost()) {

			$formData = $request->getPost();

			$stmt = $dbh->prepare('SELECT * FROM signup_codes WHERE code = :code AND artist_id = :artist_id AND used_by_id IS NULL LIMIT 1');
			$stmt->bindValue('code', $formData['code']);
			$stmt->bindValue('artist_id', $artistId);
			$stmt->execute();
			if (!$signupCode = $stmt->fetchObject()) {

				$errors['code'] = 'Invalid signup code or artist already registered.';
			}

			$stmt = $dbh->prepare('SELECT * FROM users WHERE name = :user LIMIT 1');
			$stmt->bindValue('user', $formData['user']);
			$stmt->execute();
			if ($existingUser = $stmt->fetchObject()) {

				$errors['user'] = 'Username already taken. Please chose another one.';
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

				$stmt = $dbh->prepare('UPDATE signup_codes SET used_by_id = :user_id WHERE code = :code LIMIT 1');
				$stmt->bindValue('user_id', $userId);
				$stmt->bindValue('code', $formData['code']);
				$stmt->execute();

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
}
