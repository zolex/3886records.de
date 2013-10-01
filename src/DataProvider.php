<?php

class DataProvider
{
	protected $dbh;
	protected $data = array();
	protected static $__instance;
	
	public static function getInstance() {
	
		if (!self::$__instance instanceof self) {
		
			self::$__instance = new self;
		}
		
		return self::$__instance;
	}
	
	public function getSoundcloudParams($encrypt = false) {
	
		$params = '&color=0090ff&show_artwork=false';
		if ($encrypt) {
		
			$params = htmlentities($params);
		}
		
		return $params;
	}
	
	public function setDbh(\PDO $dbh) {

		$this->dbh = $dbh;
		\Query::setDbh($dbh);
	}
	
	public function getDbh() {
	
		return $this->dbh;
	}

	public function fetchAll($className, $query, $params = array()) {
	
		$stmt = $this->getDbh()->prepare($query);
		foreach ($params as $key => $value) {
		
			$stmt->bindValue($key, $value);
		}
	
		if (!$stmt->execute()) {
		
			throw new \Exception($stmt->errorInfo());
		}
		
		return $stmt->fetchAll(\PDO::FETCH_CLASS, $className);
	}

	public function fetchOne($className, $query, $params = array()) {
	
		$stmt = $this->getDbh()->prepare($query);
		foreach ($params as $key => $value) {
		
			$stmt->bindValue($key, $value);
		}
	
		if (!$stmt->execute()) {
		
			throw new \Exception($stmt->errorInfo());
		}
		
		return $stmt->fetchObject($className);
	}
	
	// 0 = all
	// 1 = upcomming
	// 2 = past
	public function getEvents($which = 1, $includeArtists = false) {
	
		if ($includeArtists) {

			$query = "SELECT e.*,
					a.id AS artist_id,
					a.key AS artist_key,
					a.name AS artist_name,
					ea.shortInfo AS artist_shortInfo
				FROM events e
				LEFT JOIN event_artists ea ON ea.event_id = e.id
				LEFT JOIN artists a ON a.id = ea.artist_id";

		} else {

			$query = "SELECT e.*
				FROM events e";
		}


		if ($which === \Models\Event::UPCOMMING) {

			$where = "WHERE e.toTime >= NOW()";
			$order = "ORDER BY e.fromTime ASC";
		
		} else if ($which === \Models\Event::PAST) {

			$where = "WHERE e.toTime < NOW()";
			$order = "ORDER BY e.fromTime DESC";

		} else {

			$where = "";
			$order = "";
		}

		$query .= " $where $order";
		
		$stmt = $this->getDbh()->prepare($query);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$lastRow = null;
		$events = array();
		while ($row = $stmt->fetchObject()) {

			if (null === $lastRow || $lastRow->id != $row->id) {

				$events[] = (new \Models\Event)->fromObject($row);
				$current = count($events) - 1;
			}

			if ($includeArtists && null !== $row->artist_id && !array_key_exists($row->artist_id, $events[$current]->artists)) {

				$events[$current]->artists[$row->artist_id] = (new \Models\Artist)->fromObject($row, 'artist_');
			}

			$lastRow = $row;
		}

		return $events;
	}
	
	public function getLabels() {
	
		return $this->fetchAll('\Models\Label', "SELECT * FROM labels");
	}
	
	public function getLabel($name) {
	
		$query = "SELECT * FROM labels l LEFT JOIN label_links ll ON ll.label_id = l.id WHERE l.`key` = :key";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('key', $name);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$label = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $label) {

				$label = (new \Models\Label)->fromObject($row);
			}

			$label->links[] = (new \Models\LabelLink)->fromObject($row);
		}

		return $label;
	}	
	
	public function getArtist($name) {
	
		$query = "SELECT a.*,
					l.id AS link_id,
					l.type AS link_type,
					l.link AS link_link,
					v.id AS video_id,
					v.link AS video_link,
					e.id AS event_id,
					e.key AS event_key,
					e.name AS event_name,
					e.fromTime AS event_fromTime,
					e.toTime AS event_toTime,
					e.shortInfo AS event_shortInfo,
					e.longInfo AS event_longInfo,
					e.flyer AS event_flyer,
					e.facebook AS event_facebook
			FROM artists a
			LEFT JOIN artist_links l ON l.artist_id = a.id
			LEFT JOIN artist_videos v ON v.artist_id = a.id
			LEFT JOIN event_artists ea ON ea.artist_id = a.id
			LEFT JOIN events e ON e.id = ea.event_id
			WHERE a.`key` = :key
			  AND (e.toTime >= NOW() OR e.id IS NULL)
			ORDER BY l.position, v.position, e.fromTime";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('key', $name);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$artist = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $artist) {

				$artist = (new \Models\Artist)->fromObject($row);
			}

			if (null !== $row->link_id && !array_key_exists($row->link_id, $artist->links)) {

				$artist->links[$row->link_id] = (new \Models\ArtistLink)->fromObject($row, 'link_');
			}

			if (null !== $row->video_id && !array_key_exists($row->video_id, $artist->videos)) {

				$artist->videos[$row->video_id] = (new \Models\ArtistVideo)->fromObject($row, 'video_');
			}

			if (null !== $row->event_id && !array_key_exists($row->event_id, $artist->events)) {

				$artist->events[$row->event_id] = (new \Models\Event)->fromObject($row, 'event_');
			}
		}

		return $artist;
	}
	
	public function getArtists() {
	
		return $this->fetchAll('\Models\Artist', "SELECT * FROM artists ORDER BY position");
	}

	public function getGenre($name) {
	
		return $this->fetchOne('\Models\Genre', "SELECT * FROM genres where `key` = :key", array('key' => $name));
	}
	
	public function getGenreById($id) {
	
		return $this->fetchOne('\Models\Genre', "SELECT * FROM genres where id = :id", array('id' => $id));
	}
	
	public function getGenres() {
	
		return $this->fetchAll('\Models\Genre', "SELECT * FROM genres");
	}	
	
	public function getReleases($type) {
	
		if ($type == 'all') {

			$query = "SELECT r.*,
				a.id AS artist_id,
				a.key AS artist_key,
				a.name AS artist_name,
				g.id AS genre_id,
				g.name AS genre_name,
				l.id AS link_id,
				l.shopName AS link_shopName,
				l.link AS link_link
			FROM releases r
			LEFT JOIN artists a ON a.id = r.artist_id
			LEFT JOIN genres g ON g.id = r.genre_id
			LEFT JOIN release_links l ON l.release_id = r.id
			WHERE r.date < CURRENT_DATE()
			ORDER BY r.date DESC";

		} else if ($type == 'upcomming') {

			$query = "SELECT r.*,
				a.id AS artist_id,
				a.key AS artist_key,
				a.name AS artist_name,
				g.id AS genre_id,
				g.name AS genre_name,
				l.id AS link_id,
				l.shopName AS link_shopName,
				l.link AS link_link
			FROM releases r
			LEFT JOIN artists a ON a.id = r.artist_id
			LEFT JOIN genres g ON g.id = r.genre_id
			LEFT JOIN release_links l ON l.release_id = r.id
			WHERE r.date >= CURRENT_DATE() OR r.date IS NULL
			ORDER BY IF(r.date IS NULL, 1, 0) ASC, r.date ASC";
		}

		$stmt = $this->getDbh()->prepare($query);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$lastRow = null;
		$releases = array();
		while ($row = $stmt->fetchObject()) {

			if (null === $lastRow || $lastRow->id != $row->id) {

				$releases[] = (new \Models\Release)->fromObject($row);
				$current = count($releases) - 1;
			}

			if (null !== $row->artist_id && null === $releases[$current]->artist) {

				$releases[$current]->artist = (new \Models\Artist)->fromObject($row, 'artist_');
			}

			if (null !== $row->genre_id && null === $releases[$current]->genre) {

				$releases[$current]->genre = (new \Models\Genre)->fromObject($row, 'genre_');
			}

			if (null !== $row->link_id && !array_key_exists($row->link_id, $releases[$current]->links)) {

				$releases[$current]->links[$row->link_id] = (new \Models\ReleaseLink)->fromObject($row, 'link_');
			}

			$lastRow = $row;
		}

		return $releases;
	}
	
	public function getSubscription($value) {
	
		$query = "SELECT s.*,
					g.id AS genre_id,
					g.name AS genre_name
			FROM subscriptions s
			LEFT JOIN subscription_genres sg ON sg.subscription_id = s.id
			LEFT JOIN genres g ON g.id = sg.genre_id";
			
		if (is_string($value)) {
		
			$query .= " WHERE s.email = :email";
			$stmt = $this->getDbh()->prepare($query);
			$stmt->bindValue('email', $value);
			
		} else if (is_array($value)) {
		
			$query .= " WHERE";
			foreach ($value as $param => $search) {
			
				$query .= " `$param` = :$param";
			}
			
			$stmt = $this->getDbh()->prepare($query);
			foreach ($value as $param => $search) {
			
				$stmt->bindValue($param, $search);
			}
		}
			
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$subscription = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $subscription) {

				$subscription = (new \Models\Subscription)->fromObject($row);
			}

			if (null !== $row->genre_id && !array_key_exists($row->genre_id, $subscription->genres)) {

				$subscription->genres[$row->genre_id] = (new \Models\Genre)->fromObject($row, 'genre_');
			}
		}

		return $subscription;
	}

	public function saveSubscription($subscription) {
	
		if (null === $subscription->id) {
		
			$this->insertSubscription($subscription);
			
		} else {
		
			$this->updateSubscription($subscription);
		}
	}
	
	public function generateSubscriptionHash(&$subscription) {
	
		$hash = null;
		$stmt = $this->dbh->prepare("SELECT COUNT(*) FROM subscriptions WHERE hash = :hash");
		do {
		
			$hash = md5(uniqid($subscription->email . microtime()));
			$stmt->bindValue('hash', $hash);
			$stmt->execute();
		
		} while(0 < (integer)$stmt->fetchColumn());
		
		$subscription->hash = $hash;
	}
	
	public function insertSubscription(&$subscription) {

		$this->generateSubscriptionHash($subscription);
	
		$stmt = $this->dbh->prepare("INSERT INTO subscriptions (email, firstname, lastname, alias, newsletter, promotions, active, hash) VALUES(:email, :firstname, :lastname, :alias, :newsletter, :promotions, :active, :hash);"); 
		$stmt->bindValue('email', $subscription->email);
		$stmt->bindValue('firstname', $subscription->firstname);
		$stmt->bindValue('lastname', $subscription->lastname);
		$stmt->bindValue('alias', $subscription->alias);
		$stmt->bindValue('newsletter', $subscription->newsletter);
		$stmt->bindValue('promotions', $subscription->promotions);
		$stmt->bindValue('active', $subscription->active);
		$stmt->bindValue('hash', $subscription->hash);
		
		try { 
		
			$this->dbh->beginTransaction(); 
			$stmt->execute();
			$subscription->id = $this->dbh->lastInsertId(); 
			
			foreach ($subscription->genres as $genre) {
			
				$stmt = $this->dbh->prepare("INSERT INTO subscription_genres (subscription_id, genre_id) VALUES (:subscription_id, :genre_id);");
				$stmt->bindValue('subscription_id', $subscription->id);
				$stmt->bindValue('genre_id', $genre->id);
				$stmt->execute();
			}
			
			$this->dbh->commit(); 
			
		} catch (\PDOExecption $e) { 
		
			$this->dbh->rollback(); 
			throw $e;
		} 
	}
	
	public function updateSubscription(&$subscription) {
	
		$stmt = $this->dbh->prepare("UPDATE subscriptions set email = :email, firstname = :firstname, lastname = :lastname, alias = :alias, newsletter = :newsletter, promotions = :promotions, active = :active, hash = :hash WHERE id = :id LIMIT 1;"); 
		$stmt->bindValue('id', $subscription->id);
		$stmt->bindValue('email', $subscription->email);
		$stmt->bindValue('firstname', $subscription->firstname);
		$stmt->bindValue('lastname', $subscription->lastname);
		$stmt->bindValue('alias', $subscription->alias);
		$stmt->bindValue('newsletter', $subscription->newsletter);
		$stmt->bindValue('promotions', $subscription->promotions);
		$stmt->bindValue('active', $subscription->active);
		$stmt->bindValue('hash', $subscription->hash);
		
		try { 
		
			$this->dbh->beginTransaction(); 
			$stmt->execute();
			
			$stmt = $this->dbh->prepare("DELETE FROM subscription_genres WHERE subscription_id = :subscription_id;");
			$stmt->bindValue('subscription_id', $subscription->id);
			$stmt->execute();
			
			foreach ($subscription->genres as $genre) {
			
				$stmt = $this->dbh->prepare("INSERT INTO subscription_genres (subscription_id, genre_id) VALUES (:subscription_id, :genre_id);");
				$stmt->bindValue('subscription_id', $subscription->id);
				$stmt->bindValue('genre_id', $genre->id);
				$stmt->execute();
			}
			
			$this->dbh->commit(); 
			
		} catch (\PDOExecption $e) { 
		
			$this->dbh->rollback(); 
			throw $e;
		} 
	}
}