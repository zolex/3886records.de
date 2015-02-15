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
		
		// get-request, email encoded in route
		if ($email = $request->getParam('email', false)) {

			$email = urldecode($email);
			$email = base64_decode($email);
			
			if ($subscription = $dp->getSubscription($email)) {
			
				if ($subscription->active) {
				
					$message = 'You are already subscribed to our mailinglist.';
					$info = 'You can <a href="/subscriptions/manage/'. $subscription->hash .'">update or cancel your subscription</a> easily.';
					$success = true;
					
				} else {

					$message = 'Please activate your subscription.';
					$info = 'We\'ve sent a confirmation email to "'. $subscription->email .'". Please click the activation link to confirm your email address.';
					$success = true;
				}
			}
		
		} else if (!$request->isPost()) {
		
			$showForm = true;
			
		} else if (!$email = $request->getPost('email')) {
		
			$message = 'No email address provided';
		
		} else {
		
			if ($subscription = $dp->getSubscription($email)) {

				if ($subscription->active == 0) {

					$dp->generateSubscriptionHash($subscription);
					$this->sendActivationEmail($subscription, $request->getConfig('smtp'));
				}
			
			} else {
			
				$subscription = new \Models\Subscription;
				$subscription->email = $email;
				$dp->generateSubscriptionHash($subscription);

				$this->sendActivationEmail($subscription, $request->getConfig('smtp'));
			}
			
			$dp->saveSubscription($subscription);
			header('Location: /subscriptions/confirm/'. urlencode(base64_encode($subscription->email)));
			exit;
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
			
			if (1 == $request->getParam('state')) {
			
				$message = 'Thank your for subscribing to our newsletter and promotional mailing list.';
			
			} else {
			
				$message = 'You are now unsubscribed from our mailing list.';
			}
		
		} else if ($token = $request->getParam('token', false)) {
		
			if ($subscription = $dp->getSubscription(array('hash' => $token))) {

				$subscription->active = 1;
				$dp->generateSubscriptionHash($subscription);
				$dp->saveSubscription($subscription);
				
				$this->sendWelcomeEmail($subscription, $request->getConfig('smtp'));
				
				header('Location: /subscriptions/done/1');
				exit;
				
			} else {
			
				header('Location: /subscriptions');
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
	
	public function manage($request) {
	
		$dp = $this->getDataProvider();
		$message = null;
		
		if ($token = $request->getParam('token', false)) {
		
			if ($subscription = $dp->getSubscription(array('hash' => $token))) {

				if ($request->isPost()) {
			
					//$subscription->email = $request->getPost('email');
					$subscription->firstname = $request->getPost('firstname');
					$subscription->lastname = $request->getPost('lastname');
					$subscription->alias = $request->getPost('alias');
					$subscription->newsletter = $request->getPost('newsletter');
					$subscription->promotions = 0 == count($genreIds) ? 0 : 1;
					$subscription->genres = array();
				
					if (null !== $request->getPost('update')) {
					
						$subscription->active = 1;
					
					} else if (null !== $request->getPost('cancel')) {
					
						$subscription->active = 0;
					}
				
					$dp->saveSubscription($subscription);
				
					header('Location: /subscriptions/done/'. $subscription->active);
					exit;
				}
				
			} else {
			
				header('Location: /subscriptions');
				exit;
			}
			
		} else {
			
			header('Location: /subscriptions');
			exit;
		}

		return array(
			'metaTitle' => 'Subscriptions',
			'genres' => $dp->getGenres(),
			'subscription' => $subscription,
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
	
	protected function sendActivationEmail($subscription, $config) {
	
		$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
		  ->setUsername($config['user'])
		  ->setPassword($config['password']);

		$mailer = \Swift_Mailer::newInstance($transport);
		$message = \Swift_Message::newInstance();
		$body = \ViewLoader::load('email/subscription_confirm', array(
			'subscription' => $subscription,
		));

		$message->setSubject('Confirm 3886records subscription')
		  ->setFrom(array('noreply@3886records.de' => '3886records'))
		  ->setTo(array($subscription->email))
		  ->setBody($body, 'text/html');

		$mailer->send($message);
	}
	
	protected function sendWelcomeEmail($subscription, $config) {
	
		$transport = \Swift_SmtpTransport::newInstance($config['host'], $config['port'])
		  ->setUsername($config['user'])
		  ->setPassword($config['password']);

		$mailer = \Swift_Mailer::newInstance($transport);
		$message = \Swift_Message::newInstance();
		$body = \ViewLoader::load('email/subscription_manage', array(
			'subscription' => $subscription,
		));

		$message->setSubject('Welcome to 3886records')
		  ->setFrom(array('noreply@3886records.de' => '3886records'))
		  ->setTo(array($subscription->email))
		  ->setBody($body, 'text/html');

		$mailer->send($message);
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
