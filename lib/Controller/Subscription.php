<?php

namespace Controller;

class Subscription extends ControllerAction
{
	public function subscribe($request) {
	
		$dp = $this->getDataProvider();
	
		$showForm = false;
		$message = null;
		$success = false;
		$info = null;
		
		if ($email = $request->getParam('email', false)) {

			$email = urldecode($email);
			$email = base64_decode($email);
			
			if ($subscription = $dp->getSubscription($email)) {
			
				$success = true;
				$message = 'Please activate your subscription.';
				$info = 'We\'ve sent a confirmation email to "'. $subscription->email .'". Please click the activation link to confirm your email address.';
			}
		
		} else if (!$request->isPost()) {
		
			$showForm = true;
			
		} else if (!$email = $request->getPost('email')) {
		
			$message = 'No email address provided';
			
		} else {
		
			if (!$subscription = $dp->getSubscription($email)) {
			
				$subscription = new \Models\Subscription;
				$subscription->email = $email;
				
				$genres = $dp->getGenres();
				foreach ($genres AS $genre) {
				
					$subscription->genres[] = $genre;
				}
				
				$dp->saveSubscription($subscription);
				$this->sendActivationEmail($subscription);
				
				header('Location: /subscriptions/confirm/'. urlencode(base64_encode($subscription->email)));
				exit;
				
			} else {
			
				if ($subscription->active) {
				
					$message = 'You are already subscribed to our mailinglist.';
					$success = true;
					
				} else {
				
					$this->sendActivationEmail($subscription);
					header('Location: /subscriptions/confirm/'. urlencode(base64_encode($subscription->email)));
					exit;
				}
			}
		}
		
		return array(
			'metaTitle' => 'Subscriptions',
			'success' => $success,
			'message' => $message,
			'info' => $info,
			'showForm' => $showForm,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'url' => '/subscriptions',
					'title' => 'Subscriptions',
				),
			),
		);
	}
	
	public function activate($request) {
	
		$dp = $this->getDataProvider();
	
		$showForm = false;
		$message = null;
		$success = false;
		$info = null;
		
		if ($request->getParam('done', false)) {

			$success = true;
			$message = 'Thank your for subscribing to our newsletter and promotional mailing list.';
		
		} else if ($token = $request->getParam('token', false)) {
		
			if ($subscription = $dp->getSubscription(array('hash' => $token))) {

				$subscription->active = 1;
				$subscription->hash = null;
				$dp->saveSubscription($subscription);
				
				header('Location: /subscriptions/done');
				exit;
				
			} else {
			
				header('Location: /subscriptions/subscribe');
				exit;
			}
		}
		
		return array(
			'metaTitle' => 'Subscriptions',
			'success' => $success,
			'message' => $message,
			'info' => $info,
			'showForm' => $showForm,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'url' => '/subscriptions',
					'title' => 'Subscriptions',
				),
			),
		);
	}
	
	protected function sendActivationEmail($subscription) {
	
		
	}
/*
	public function index($request) {
        
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
*/
}
