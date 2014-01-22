<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
$dp = DataProvider::getInstance()->setDbh($dbh);

$transport = Swift_SmtpTransport::newInstance($config['smtp']['host'], $config['smtp']['port'])
	->setUsername($config['smtp']['user'])
	->setPassword($config['smtp']['password']);

$mailer = Swift_Mailer::newInstance($transport);


die("DISABLED-CHECK-VALUES-FOR-NEXT-MAILING!");

$promotion = $dp->getPromotionByKey('zwielicht-shadowplay-ep');

$stmt = $dbh->prepare("SELECT email FROM subscriptions WHERE active = 1 ORDER BY email DESC");
if (!$stmt->execute()) {

	die("mysql error"); 
}

while ($row = $stmt->fetchObject()) {

	$subscription = $dp->getSubscription($row->email);

	$message = Swift_Message::newInstance();
	$body = ViewLoader::load('email/newsletter', array(
		'promotion' => $promotion,
		'subscription' => $subscription,
		'images' => (object)array(
			'banner' => $message->embed(Swift_Image::fromPath('views/email/images/'. $promotion->banner)),
			'image2' => $message->embed(Swift_Image::fromPath('views/email/images/'. $promotion->image2)),
			'image3' => $message->embed(Swift_Image::fromPath('views/email/images/'. $promotion->image3)),
			'like' => $message->embed(Swift_Image::fromPath('views/email/images/like-glyph.png')),
			'tweet' => $message->embed(Swift_Image::fromPath('views/email/images/tweet-glyph.png')),
		
		),
	));

	$message->setSubject($promotion->title)
		->setFrom(array('noreply@3886records.de' => '3886records'))
		->setTo(array($subscription->email => $subscription->getName()))
		->setBody($body, 'text/html');

	$mailer->send($message);

	echo $subscription->email . PHP_EOL;
}

