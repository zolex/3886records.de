<?php

namespace Controller;

use DataProvider;

class Promotion extends ControllerAction
{
	public function thanks($request) {

		$promoKey = $request->getParam('key');
		$promoToken = $request->getParam('token');
	
		$dp = \DataProvider::getInstance();
		$promotion = $dp->getPromotionByKey($promoKey);
		$subscription = $dp->getSubscription(array('hash' => $promoToken));

		if (!$promotion || !$subscription) {

			die("no promo or subscr");
			header('Location: /');
			exit;
		}
		
		if ($promotion->isExpired()) {
		
			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash);
			exit;
		}

		$feedback = $dp->getPromotionFeedback($promotion, $subscription);
		if (!$feedback->updated_at) {

			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash);
			exit;
		}

		if (!isset($_GET['exceeded']) && $feedback->availableDownloads() == 0) {

			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash . '/thanks?exceeded');
			exit;
		}

		return array(
			'metaTitle' => 'Promotion feedback',
			'promotion' => $promotion,
			'subscription' => $subscription,
			'feedback' => $feedback,
			'exceeded' =>  isset($_GET['exceeded']),
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Promotions',
				),
			),
		);
	}

	public function view($request) {

		$promoKey = $request->getParam('key');
		if ($promoKey == 'spekta-lite-prog' || $promoKey == 'spekta-lite-prog-test') {

			$promoKey = 'spekta-liteprog';
		}

		$promoToken = $request->getParam('token');
	
		$dp = \DataProvider::getInstance();

		$promotion = $dp->getPromotionByKey($promoKey);
		if (!$promotion) {
		
			header('Location: /');
			exit;
		}


		$newSubscriber = false;
		if (!$subscription = $dp->getSubscription(array('hash' => $promoToken))) {

			if ($request->isPost() && $email = $request->getPost('email')) {

				$subscription = $dp->getSubscription(array('email' => $email));
			}

			if (!$subscription) {

				$subscription = new \Models\Subscription;
			}

			$newSubscriber = true;
		}

		// find feedback or create new subscription with feedback
		if (!$feedback = $dp->getPromotionFeedback($promotion, $subscription)) {

			$feedback = new \Models\PromotionFeedback;
			$feedback->subscription = $subscription;
		}

		// mark feedback as viewed
		if (!$feedback->viewed && !$newSubscriber) {

			$feedback->viewed = 1;
			$dp->savePromotionFeedback($feedback);
		}

		$messages = array();
		if ($request->isPost()) {

			$subscription->firstname = $request->getPost('firstname');
			$subscription->lastname = $request->getPost('lastname');
			if (!$subscription->email) {

				$subscription->email = $request->getPost('email');
			}

			if (!$subscription->email) {

				$messages['email'] = 'Please enter your email.';
			}

			if (!$subscription->alias = $request->getPost('alias')) {

				$messages['alias'] = 'Please enter your alias.';
			}

			if (!$subscription->usertype = $request->getPost('usertype')) {

				$messages['usertype'] = 'Please tell us how you are related to music.';
			}

			if (!$feedback->support = $request->getPost('support')) {

				$messages['support'] = 'Please choose whether you are going to support the releae or not.';
			}

			if (count($promotion->tracks) > 1 && !$feedback->best_track_id = $request->getPost('bestTrack')) {

				$messages['bestTrack'] = 'Please select your favorite track of the release.';
			}

			if (!$feedback->rating = $request->getPost('rating')) {

				$messages['rating'] = 'Please select an overall rating for the release.';
			}

			$feedback->review = $request->getPost('review');

			if (!count($messages)) {

				$dp->saveSubscription($subscription);
				$feedback->subscription = $subscription;
				$feedback->updated_at = date("Y-m-d H:i:s");
				$dp->savePromotionFeedback($feedback);

				header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash . '/thanks');
				exit;
			}
		}

		// already has feedback, continue to download page
		if ($feedback->updated_at) {

			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash . '/thanks');
			exit;
		}

		return array(
			'metaTitle' => $promotion->title,
			'facebookImage' => 'http://www.3886records.de/img/releases/'. $promotion->release->cover,
			'title' => $promotion->release->title,
			'promotion' => $promotion,
			'subscription' => $subscription,
			'feedback' => $feedback,
			'errors' => $messages,
			'breadcrumb' => array(
				(object)array(
					'url' => '/',
					'title' => 'Home',
				),
				(object)array(
					'active' => true,
					'title' => 'Promotions',
				),
			),
		);
	}
	
	public function webview($request) {
	
		$promoKey = $request->getParam('key');
		$promoToken = $request->getParam('token');
	
		$dp = \DataProvider::getInstance();
		$promotion = $dp->getPromotionByKey($promoKey);
		$subscription = $dp->getSubscription(array('hash' => $promoToken));
	
		if (!$promotion || !$subscription) {
		
			header('Location: /');
			exit;
		}
	
		$body = \ViewLoader::load('email/newsletter', array(
			'promotion' => $promotion,
			'subscription' => $subscription,
			'images' => (object)array(
				'banner' => '/img/email/'. $promotion->banner,
				'image2' => '/img/email/'. $promotion->image2,
				'image3' => '/img/email/'. $promotion->image3,
				'like' => '/img/email/like-glyph.png',
				'tweet' => '/img/email/tweet-glyph.png',
			),
		));
	
		return array(
			'noLayout' => true,
			'content' => $body,
		);
	}

	public function download($request) {

		$promoKey = $request->getParam('key');
		$promoToken = $request->getParam('token');
	
		$dp = \DataProvider::getInstance();
		$promotion = $dp->getPromotionByKey($promoKey);
		$subscription = $dp->getSubscription(array('hash' => $promoToken));

		if (!$promotion || !$subscription) {
		
			header('Location: /');
			exit;
		}
		
		if ($promotion->isExpired()) {
		
			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash);
			exit;
		}

		$feedback = $dp->getPromotionFeedback($promotion, $subscription);
		if (!$feedback->viewed || !$feedback->updated_at) {

			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash);
			exit;
		}

		if ($feedback->downloads >= 3) {

			header('Location: /promotion/'. $promotion->key . '/'. $subscription->hash .'/thanks?exceeded');
			exit;
		}

		$feedback->downloads++;
		$dp->savePromotionFeedback($feedback);		

		header('Content-Type: application/csv');
		header('Content-Disposition: attachment; filename='. $promotion->title .'.zip');
		header("Content-Transfer-Encoding: binary");
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Length: '. filesize($promotion->download));
		readfile($promotion->download);
		exit;
	}
}
