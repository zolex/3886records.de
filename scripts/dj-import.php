<?php

chdir(dirname(dirname(__FILE__)));
$config = (require 'config.php');
require 'vendor/autoload.php';

$dbh = new PDO('mysql:host='. $config['db']['host'] .';port='. $config['db']['port'] .';dbname='. $config['db']['name'] .';charset=utf8', $config['db']['user'], $config['db']['password']);
DataProvider::getInstance()->setDbh($dbh);

$stmt = $dbh->prepare("SELECT * FROM genres");
$stmt->execute();
$genres = array();
while($genre = $stmt->fetchObject('\Models\Genre')) {

	$genres[] = $genre;
}

$existing = 0;
$added = 0;

$handle = @fopen("shared/data/DJ_List.csv", "r");
if (false !== $handle) {

    while (false !== ($buffer = fgets($handle, 4096))) {

        $fields = explode(',', $buffer);
        foreach ($fields as &$field) {

        	$field = trim($field, "\r\n\t\" ");
        }

        $stmt = $dbh->prepare("SELECT COUNT(*) FROM subscriptions WHERE email = :email");
        $stmt->bindValue('email', $fields[1]);
        $stmt->execute();
        if (0 < (integer)($stmt->fetchColumn())) {

            $existing++;
            continue;
        }

        $hash = null;
		$stmt = $dbh->prepare("SELECT COUNT(*) FROM subscriptions WHERE hash = :hash");
		do {
		
			$hash = md5(uniqid($fields[1] . microtime()));
			$stmt->bindValue('hash', $hash);
			$stmt->execute();
		
		} while(0 < (integer)$stmt->fetchColumn());

        $added++;

        $stmt = $dbh->prepare("INSERT INTO subscriptions (email, alias, newsletter, promotions, active, hash) VALUES(:email, :alias, 1, 1, 1, :hash);");
        $stmt->bindValue('alias', empty($fields[0]) ? null : $fields[0]);
        $stmt->bindValue('email', $fields[1]);
        $stmt->bindValue('hash', $hash);
        $stmt->execute();

        $subscriptionId = $dbh->lastInsertId();
        foreach ($genres as $genre) {

        	$stmt = $dbh->prepare("INSERT INTO subscription_genres (subscription_id, genre_id) VALUES(:sid, :gid)");
        	$stmt->bindValue('sid', $subscriptionId);
            $stmt->bindValue('gid', $genre->id);
            $stmt->execute();
        }

    }

    fclose($handle);
}

echo "added $added emails. $existing already existed\r\n";