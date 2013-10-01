<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

//$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
//DataProvider::getInstance()->setDbh($dbh);

$transport = Swift_SmtpTransport::newInstance($config['smtp']['host'], $config['smtp']['port'])
  ->setUsername($config['smtp']['user'])
  ->setPassword($config['smtp']['password']);

$mailer = Swift_Mailer::newInstance($transport);
$message = Swift_Message::newInstance();
$body = ViewLoader::load('email/newsletter', array(
	'images' => (object)array(
		'logo' => $message->embed(Swift_Image::fromPath('views/email/images/3886logo.jpg')),
		'like' => $message->embed(Swift_Image::fromPath('views/email/images/like-glyph.png')),
		'tweet' => $message->embed(Swift_Image::fromPath('views/email/images/tweet-glyph.png')),
		'forward' => $message->embed(Swift_Image::fromPath('views/email/images/forward-glyph.png')),
	),
	'test' => 'Lorem Ipsum',
));

$message->setSubject('Your subject')
  ->setFrom(array('noreply@3886records.de' => '3886records'))
  ->setTo(array('zlx@gmx.de' => 'Andreas Linden'))
  ->setBody($body, 'text/html');

$mailer->send($message);
