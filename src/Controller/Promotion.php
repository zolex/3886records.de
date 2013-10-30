<?php

namespace Controller;

use DataProvider;

class Promotion extends ControllerAction
{
	public function view($request) {

		$promoKey = $request->getParam('key');
		$promoToken = $request->getParam('token');
	
		$dp = \DataProvider::getInstance();
		$promotion = $dp->getPromotionByKey($promoKey);
		$subscription = $dp->getSubscription(array('hash' => $promoToken));

		if (!$promotion || !$subscription) {
		
			header('Location: /');
			exit;
		}

		$feedback = $dp->getPromotionFeedback($promotion, $subscription);
		if (!$feedback->viewed) {

			$feedback->viewed = 1;
			$dp->savePromotionFeedback($feedback);
		}

		return array(
			'metaTitle' => $promotion->title,
			'title' => $promotion->release->title,
			'promotion' => $promotion,
			'subscription' => $subscription,
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
				'forward' => '/img/email/forward-glyph.png',
				
			),
			'test' => 'Lorem Ipsum',
		));
	
		return array(
			'noLayout' => true,
			'content' => $body,
		);
	}
}
