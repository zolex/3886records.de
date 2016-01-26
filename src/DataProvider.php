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
		return $this;
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
		
			throw new \Exception();
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
	
	public function getNextEvent() {

		$query = $query = "SELECT e.* FROM events e WHERE e.toTime >= NOW() ORDER BY fromTime ASC LIMIT 1;";
		$stmt = $this->getDbh()->prepare($query);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}

		$event = null;
		if ($row = $stmt->fetchObject()) {

			$event = (new \Models\Event)->fromObject($row, null, true);
		}

		return $event;
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

			$where = "WHERE e.toTime >= NOW() AND e.visible = 1";
			$order = "ORDER BY e.fromTime ASC";
		
		} else if ($which === \Models\Event::PAST) {

			$where = "WHERE e.toTime < NOW() AND e.visible = 1";
			$order = "ORDER BY e.fromTime DESC";

		} else {

			$where = "WHERE e.visible = 1";
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

				$events[] = (new \Models\Event)->fromObject($row, null, true);
				$current = count($events) - 1;
			}

			if ($includeArtists && (null !== $row->artist_id || null !== $row->artist_shortInfo) && !array_key_exists($row->artist_id, $events[$current]->artists)) {

				if ($row->artist_id) {

					$events[$current]->artists[$row->artist_id] = (new \Models\Artist)->fromObject($row, 'artist_');

				} else {

					$events[$current]->artists[] = (new \Models\Artist)->fromObject($row, 'artist_');
				}
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

				$label = (new \Models\Label)->fromObject($row, null, true);
			}

			$label->links[] = (new \Models\LabelLink)->fromObject($row);
		}

		return $label;
	}	
	
	public function getLabelByName($name) {
	
		$query = "SELECT * FROM labels WHERE name = :name";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('name', $name);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$label = null;
		if ($row = $stmt->fetchObject()) {
		
			$label = (new \Models\Label)->fromObject($row);
		}
		
		return $label;
	}	
	
	public function getSalesReportByQuarter($quarter) {
	
		$query = "SELECT * FROM sales_reports WHERE quarter = :quarter";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('quarter', $quarter);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$report = null;
		if ($row = $stmt->fetchObject()) {
		
			$report = (new \Models\SalesReport)->fromObject($row);
		}
		
		return $report;
	}
	
	public function getArtist($name) {
	
		return \Domains\Artists::getInstance()
			->setDbh($this->getDbh())
			->findOneByKey($name);
	}
	
	public function getArtistByName($name) {
	
		$query = "SELECT * FROM artists WHERE lw_name = :name OR name = :name";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('name', $name);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$artist = null;
		if ($row = $stmt->fetchObject()) {

			$artist = (new \Models\Artist)->fromObject($row, null);
		}

		return $artist;
	}
	
	public function getArtistById($id) {
	
		$query = "SELECT * FROM artists WHERE id = :id";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('id', $id);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$artist = null;
		if ($row = $stmt->fetchObject()) {

			$artist = (new \Models\Artist)->fromObject($row);
		}

		return $artist;
	}
	
	public function getArtists() {
	
		return $this->fetchAll('\Models\Artist', "SELECT * FROM artists WHERE type = 0 AND visible = 1 ORDER BY name ASC");
	}

	public function getDJs() {
	
		return $this->fetchAll('\Models\Artist', "SELECT * FROM artists WHERE type = 1 AND visible = 1 ORDER BY name ASC");
	}

	public function getGenre($name) {
	
		$query = "SELECT g.*,
					sg.id AS sub_id,
					sg.name AS sub_name,
					sg.`key` AS sub_key,
					pg.id AS parent_id,
					pg.name AS parent_name,
					pg.`key` AS parent_key
				FROM genres g
				LEFT JOIN genre_relations gr ON gr.parent_id = g.id
				LEFT JOIN genres sg ON sg.id = gr.genre_id
				LEFT JOIN genre_relations gr2 ON gr2.genre_id = g.id
				LEFT JOIN genres pg ON pg.id = gr2.parent_id
				WHERE g.`key` = :key";

		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('key', $name);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}


		$genre = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $genre) {

				$genre = (new \Models\Genre)->fromObject($row, null, true);
			}

			if (null !== $row->sub_id && !array_key_exists($row->sub_id, $genre->subgenres)) {

				$genre->subgenres[$row->sub_id] = (new \Models\Genre)->fromObject($row, 'sub_');
			}

			if (null !== $row->parent_id && !array_key_exists($row->parent_id, $genre->superiors)) {

				$genre->superiors[$row->parent_id] = (new \Models\Genre)->fromObject($row, 'parent_');
			}
		}

		return $genre;
	}
	
	public function getGenreById($id) {
	
		return $this->fetchOne('\Models\Genre', "SELECT * FROM genres where id = :id", array('id' => $id));
	}
	
	public function getGenres() {
	
		$query = "SELECT g.*,
					sg.id AS sub_id,
					sg.name AS sub_name,
					sg.`key` AS sub_key,
					pg.id AS parent_id,
					pg.name AS parent_name,
					pg.`key` AS parent_key,
					a.id AS artist_id,
					a.`key` AS artist_key,
					a.name AS artist_name
				FROM genres g
				LEFT JOIN genre_relations gr ON gr.parent_id = g.id
				LEFT JOIN genres sg ON sg.id = gr.genre_id
				LEFT JOIN genre_relations gr2 ON gr2.genre_id = g.id
				LEFT JOIN genres pg ON pg.id = gr2.parent_id
				LEFT JOIN artist_genres AS ag ON ag.genre_id = g.id
				LEFT JOIN artists AS a ON a.id = ag.artist_id
				ORDER BY g.position ASC, sg.position ASC, a.name ASC";

		$stmt = $this->getDbh()->prepare($query);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}


		$lastRow = null;
		$genres = array();
		while ($row = $stmt->fetchObject()) {

			if (null === $lastRow || $lastRow->id != $row->id) {

				$genres[] = (new \Models\Genre)->fromObject($row, null, true);
				$current = count($genres) - 1;
			}

			if (null !== $row->sub_id && !array_key_exists($row->sub_id, $genres[$current]->subgenres)) {

				$genres[$current]->subgenres[$row->sub_id] = (new \Models\Genre)->fromObject($row, 'sub_');
			}

			if (null !== $row->parent_id && !array_key_exists($row->parent_id, $genres[$current]->superiors)) {

				$genres[$current]->superiors[$row->parent_id] = (new \Models\Genre)->fromObject($row, 'parent_');
			}

			if (null !== $row->artist_id && !array_key_exists($row->artist_id, $genres[$current]->artists)) {

				$genres[$current]->artists[$row->artist_id] = (new \Models\Artist)->fromObject($row, 'artist_');
			}

			$lastRow = $row;
		}

		return $genres;
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
			INNER JOIN artists a ON a.id = r.artist_id
			INNER JOIN genres g ON g.id = r.genre_id
			LEFT JOIN release_links l ON l.release_id = r.id
			WHERE r.date <= CURRENT_DATE()
			  AND r.visible = 1
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
			INNER JOIN artists a ON a.id = r.artist_id
			INNER JOIN genres g ON g.id = r.genre_id
			LEFT JOIN release_links l ON l.release_id = r.id
			WHERE (r.date > CURRENT_DATE() OR r.date IS NULL)
			  AND r.visible = 1
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

				$releases[] = (new \Models\Release)->fromObject($row, null, true);
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
	
	public function getReleaseByCatalog($catalog) {
	
		$query = "SELECT * FROM releases WHERE catalog = :catalog";
		$stmt = $this->getDbh()->prepare($query);
		$stmt->bindValue('catalog', $catalog);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$release = null;
		if ($row = $stmt->fetchObject()) {

			$release = (new \Models\Release)->fromObject($row);
		}

		return $release;
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

				$subscription = (new \Models\Subscription)->fromObject($row, null, true);
			}

			if (null !== $row->genre_id && !array_key_exists($row->genre_id, $subscription->genres)) {

				$subscription->genres[$row->genre_id] = (new \Models\Genre)->fromObject($row, 'genre_');
			}
		}

		return $subscription;
	}

	public function saveSubscription($subscription) {
	
		if (!$subscription->id) {
		
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
	
		$stmt = $this->dbh->prepare("INSERT INTO subscriptions (email, firstname, lastname, alias, newsletter, promotions, active, hash, usertype) VALUES(:email, :firstname, :lastname, :alias, :newsletter, :promotions, :active, :hash, :usertype);"); 
		$stmt->bindValue('email', $subscription->email);
		$stmt->bindValue('firstname', $subscription->firstname);
		$stmt->bindValue('lastname', $subscription->lastname);
		$stmt->bindValue('alias', $subscription->alias);
		$stmt->bindValue('newsletter', $subscription->newsletter);
		$stmt->bindValue('promotions', $subscription->promotions);
		$stmt->bindValue('active', $subscription->active);
		$stmt->bindValue('hash', $subscription->hash);
		$stmt->bindValue('usertype', $subscription->usertype);
		
		try { 
		
			$this->dbh->beginTransaction(); 
			if (!$stmt->execute()) {
		
				$info = $stmt->errorInfo();
				throw new \Exception($info[2], $info[1]);
			}

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
	
		$stmt = $this->dbh->prepare("UPDATE subscriptions set email = :email, firstname = :firstname, lastname = :lastname, alias = :alias, newsletter = :newsletter, promotions = :promotions, active = :active, hash = :hash, usertype = :usertype WHERE id = :id LIMIT 1;"); 
		$stmt->bindValue('id', $subscription->id);
		$stmt->bindValue('email', $subscription->email);
		$stmt->bindValue('firstname', $subscription->firstname);
		$stmt->bindValue('lastname', $subscription->lastname);
		$stmt->bindValue('alias', $subscription->alias);
		$stmt->bindValue('newsletter', $subscription->newsletter);
		$stmt->bindValue('promotions', $subscription->promotions);
		$stmt->bindValue('active', $subscription->active);
		$stmt->bindValue('hash', $subscription->hash);
		$stmt->bindValue('usertype', $subscription->usertype);
		
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
	
	public function getPromotionByKey($key) {
	
		$stmt = $this->dbh->prepare("SELECT
				p.*,
				r.id AS release_id,
				r.title AS release_title,
				r.cover AS release_cover,
				r.date AS release_date,
				r.format AS release_format,
				r.type AS release_type,
				rl.id AS releaselink_id,
				rl.shopName AS releaselink_shopName,
				rl.link AS releaselink_link,
				a.id AS artist_id,
				a.`key` AS artist_key,
				a.name AS artist_name,
				g.id AS genre_id,
				e.id AS event_id,
				e.name AS event_name,
				e.key AS event_key,
				e.facebook AS event_facebook,
				e.flyer AS event_flyer,
				g.`key` AS genre_key,
				g.name AS genre_name,
				rs.id AS releases_id,
				rs.title AS releases_title,
				rs.cover AS releases_cover,
				rs.date AS releases_date,
				rs.format AS releases_format,
				rs.type AS releases_type,
				rsl.id AS releaseslink_id,
				rsl.shopName AS releaseslink_shopName,
				rsl.link AS releaseslink_link,
				t.id AS track_id,
				t.title AS track_title,
				t.link AS track_link
			FROM promotions p
			LEFT JOIN promotion_tracks t ON (t.promotion_id = p.id)
			INNER JOIN releases r ON r.id = p.release_id
			LEFT JOIN release_links rl ON rl.release_id = r.id
			INNER JOIN artists a ON a.id = r.artist_id
			INNER JOIN genres g ON g.id = r.genre_id
			LEFT JOIN event_artists ea ON ea.artist_id = a.id
			LEFT JOIN events e ON (e.id = ea.event_id AND e.fromTime >= NOW())
			LEFT JOIN releases rs ON (rs.artist_id = a.id AND rs.id != r.id)
			LEFT JOIN release_links rsl ON (rsl.release_id = rs.id)
			WHERE p.`key` = :key"); 
			
		$stmt->bindValue('key', $key);
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$promotion = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $promotion) {

				$promotion = (new \Models\Promotion)->fromObject($row, null, true);
			}

			if (null !== $row->genre_id && null === $promotion->genre) {

				$promotion->genre = (new \Models\Genre)->fromObject($row, 'genre_');
			}
			
			if (null !== $row->release_id && null === $promotion->release) {

				$promotion->release = (new \Models\Release)->fromObject($row, 'release_');
			}
			
			if (null !== $row->releaselink_id && null !== $promotion->release && !array_key_exists($row->releaselink_id, $promotion->release->links)) {

				$promotion->release->links[$row->releaselink_id] = (new \Models\ReleaseLink)->fromObject($row, 'releaselink_');
			}
			
			if (null !== $row->artist_id && null !== $promotion->release && null === $promotion->release->artist) {

				$promotion->release->artist = (new \Models\Artist)->fromObject($row, 'artist_');
			}

			if (null !== $row->event_id && null !== $promotion->release->artist->events && !array_key_exists($row->releases_id, $promotion->release->artist->events)) {

				 $promotion->release->artist->events[$row->event_id] = (new \Models\Event)->fromObject($row, 'event_');
			}
			
			if (null !== $row->releases_id && null !== $promotion->release->artist && !array_key_exists($row->releases_id, $promotion->release->artist->releases)) {

				 $promotion->release->artist->releases[$row->releases_id] = (new \Models\Release)->fromObject($row, 'releases_');
				 $promotion->release->artist->releases[$row->releases_id]->artist = $promotion->release->artist;
			}
			
			if (null !== $row->releaseslink_id && !array_key_exists($row->releaseslink_id, $promotion->release->artist->releases[$row->releases_id]->links)) {

				$promotion->release->artist->releases[$row->releases_id]->links[$row->releaseslink_id] = (new \Models\ReleaseLink)->fromObject($row, 'releaseslink_');
			}

			if (null !== $row->track_id && !array_key_exists($row->track_id, $promotion->tracks)) {

				$promotion->tracks[$row->track_id] = (new \Models\PromotionTrack)->fromObject($row, 'track_');
			}
		}

		return $promotion;
	}

	public function getPromotionFeedback($promotion, $subscription) {

		$stmt = $this->dbh->prepare("SELECT *
			FROM promotion_feedback
			WHERE promotion_id = :promotion_id
			  AND subscription_id = :subscription_id");

		$stmt->bindValue('promotion_id', $promotion->id);
		$stmt->bindValue('subscription_id', $subscription->id);

		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}

		$feedback = new \Models\PromotionFeedback;
		if ($row = $stmt->fetchObject()) {
	
			$feedback->fromObject($row, null, false);
		}

		if (!$feedback) {

			$feedback->promotion_id = $promotion->id;
			$feedback->subscription_id = $subscription->id;
		}

		$feedback->promotion = $promotion;
		$feedback->subscription = $subscription;

		return $feedback;
	}

	public function savePromotionFeedback($feedback) {

		if ($feedback->id) {

			$stmt = $this->dbh->prepare("UPDATE promotion_feedback SET downloads = :downloads, promotion_id = :promotion_id, subscription_id = :subscription_id, viewed = :viewed, support = :support, rating = :rating, review = :review, best_track_id = :best_track_id, updated_at = :updated_at WHERE id = :id LIMIT 1;");
			$stmt->bindValue('id', (integer)$feedback->id);
			$stmt->bindValue('promotion_id', $feedback->promotion->id);
			$stmt->bindValue('subscription_id', $feedback->subscription->id);
			$stmt->bindValue('support', $feedback->support);
			$stmt->bindValue('viewed', (integer)$feedback->viewed);
			$stmt->bindValue('rating', $feedback->rating);
			$stmt->bindValue('review', $feedback->review);
			$stmt->bindValue('best_track_id', $feedback->best_track_id);
			$stmt->bindValue('updated_at', $feedback->updated_at);
			$stmt->bindValue('downloads', $feedback->downloads);

			if (!$stmt->execute()) {
		
				$errorInfo = $stmt->errorInfo();
				throw new \Exception($errorInfo[2], $errorInfo[1]);
			}

		} else {

			$stmt = $this->dbh->prepare("INSERT INTO promotion_feedback (downloads, promotion_id, subscription_id, viewed, support, rating, review, best_track_id, updated_at, created_at) VALUES(0, :promotion_id, :subscription_id, :viewed, :support, :rating, :review, :best_track_id, :updated_at, NOW())");
			$stmt->bindValue('promotion_id', $feedback->promotion->id);
			$stmt->bindValue('subscription_id', $feedback->subscription->id);
			$stmt->bindValue('support', $feedback->support);
			$stmt->bindValue('viewed', (integer)$feedback->viewed);
			$stmt->bindValue('rating', $feedback->rating);
			$stmt->bindValue('review', $feedback->review);
			$stmt->bindValue('updated_at', $feedback->updated_at);
			$stmt->bindValue('best_track_id', $feedback->best_track_id);

			if (!$stmt->execute()) {
		
				$errorInfo = $stmt->errorInfo();
				throw new \Exception($errorInfo[2], $errorInfo[1]);
			}

			$feedback->id = $this->dbh->lastInsertId(); 
		}
	}

	public function trackQrCodeVisit($info) {
	
		$stmt = $this->dbh->prepare("INSERT INTO qrcode_visit (info, ip, client, created_at) VALUES(:info, :ip, :client, NOW());");
		$stmt->bindValue('info', $info);
		$stmt->bindValue('ip', $_SERVER['REMOTE_ADDR']);
		$stmt->bindValue('client', $_SERVER['HTTP_USER_AGENT']);
		$stmt->execute();
	}

	public function insertSweepstake($data) {

		$stmt = $this->dbh->prepare("INSERT INTO sweepstakes (fb_id, firstname, lastname, name, location, link, game, created_at) VALUES(:fb_id, :firstname, :lastname, :name, :location, :link, :game, NOW())");
		$stmt->bindValue('fb_id', $data['fb_id']);
		$stmt->bindValue('firstname', $data['firstname']);
		$stmt->bindValue('lastname', $data['lastname']);
		$stmt->bindValue('name', $data['name']);
		$stmt->bindValue('location', $data['location']);
		$stmt->bindValue('link', $data['link']);
		$stmt->bindValue('game', $data['game']);

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
	}
	
	public function insertSale(&$sale) {

		$stmt = $this->dbh->prepare("INSERT INTO sales (artist_id, label_id, release_id, report_id, release_artist, release_name, track_artist, track_title, mix_name, format, sale_type, quantity, value, deal, royalty, isrc, ean, store, track_ref) VALUES(:artist_id, :label_id, :release_id, :report_id, :release_artist, :release_name, :track_artist, :track_title, :mix_name, :format, :sale_type, :quantity, :value, :deal, :royalty, :isrc, :ean, :store, :track_ref)");

		$stmt->bindValue('artist_id', $sale->artist_id);
		$stmt->bindValue('label_id', $sale->label_id);
		$stmt->bindValue('release_id', $sale->release_id);
		$stmt->bindValue('report_id', $sale->report_id);
		$stmt->bindValue('release_artist', $sale->release_artist);
		$stmt->bindValue('release_name', $sale->release_name);
		$stmt->bindValue('track_artist', $sale->track_artist);
		$stmt->bindValue('track_title', $sale->track_title);
		$stmt->bindValue('mix_name', $sale->mix_name);
		$stmt->bindValue('format', $sale->format);
		$stmt->bindValue('sale_type', $sale->sale_type);
		$stmt->bindValue('quantity', $sale->quantity);
		$stmt->bindValue('value', $sale->value);
		$stmt->bindValue('deal', $sale->deal);
		$stmt->bindValue('royalty', $sale->royalty);
		$stmt->bindValue('isrc', $sale->isrc);
		$stmt->bindValue('ean', $sale->ean);
		$stmt->bindValue('store', $sale->store);
		$stmt->bindValue('track_ref', $sale->track_ref);

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$sale->id = $this->dbh->lastInsertId(); 
	}
	
	public function insertSalesReport(&$salesReport) {

		$stmt = $this->dbh->prepare("INSERT INTO sales_reports (name, filename, quarter, created_at) VALUES(:name, :filename, :quarter, NOW())");
		$stmt->bindValue('name', $salesReport->name);
		$stmt->bindValue('filename', $salesReport->filename);
		$stmt->bindValue('quarter', $salesReport->quarter);
		
		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$salesReport->id = $this->dbh->lastInsertId(); 
	}

	public function getNewSales($labelId = null) {

		$sql = "SELECT s.*, s.royalty AS avail FROM sales s WHERE s.invoiced = 0";
		if ($labelId !== null) {

			$sql .= " AND s.label_id = :label";
		}

		$sql .= " ORDER BY s.release_artist, s.release_name, s.format, s.sale_type";

		$stmt = $this->dbh->prepare($sql);
		if ($labelId !== null) {

			$stmt->bindValue('label', $labelId);
		}

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$sales = array();
		while ($row = $stmt->fetchObject()) {

			$sales[] = (new \Models\Sale)->fromObject($row);
		}

		return $sales;
	}
	
	public function getNewSalesByArtist($artistId) {

		$sql = "SELECT s.*, s.royalty * r.deal AS avail
			FROM sales s
			INNER JOIN releases r ON r.id = s.release_id
			WHERE s.invoiced = 0
			AND s.artist_id = :artist
			ORDER BY s.release_artist, s.release_name, s.format, s.sale_type";
			
		$stmt = $this->dbh->prepare($sql);
		$stmt->bindValue('artist', $artistId);

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$sales = array();
		while ($row = $stmt->fetchObject()) {

			$sales[] = (new \Models\Sale)->fromObject($row);
		}

		return $sales;
	}

	public function getSalesReportByLabel($labelId = null) {

		$sql = "SELECT CONCAT(l.name, ' ', s.format) AS position,
			SUM(s.quantity) AS num,
			SUM(s.royalty) AS value
			FROM sales s
			INNER JOIN labels l ON l.id = s.label_id
			WHERE s.invoiced = 0";

		if ($labelId != null) {

			$sql .= " AND s.label_id = :label";
		}

		$sql .= " GROUP BY s.label_id, s.format";

		$stmt = $this->dbh->prepare($sql);
		if ($labelId != null) {

			$stmt->bindValue('label', $labelId);
		}

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}

		$report = (object)array(

			'date' => 'xxx',
			'items' => array(),
			'labelId' => $labelId,
			'artistId' => null,
		);

		while ($row = $stmt->fetchObject()) {

			$report->items[] = $row;
		}


		$report->dates = $this->getDatesForReport($report);
		$report->date = implode(', ', $dates);

		return $report;
	}
	
	public function getDatesForReport($report) {
	
		// determine date for report
		$sql = "SELECT r.* FROM sales s INNER JOIN sales_reports r WHERE s.invoiced = 0";
		if ($report->labelId !== null) {

			$sql .= " AND s.label_id = :label";
		}
		
		if ($report->artistId !== null) {

			$sql .= " AND s.artist_id = :artist";
		}

		$sql .= " GROUP BY r.id ORDER BY r.quarter ASC";
		$stmt = $this->dbh->prepare($sql);
		if ($report->labelId != null) {

			$stmt->bindValue('label', $report->labelId);
		}
		
		if ($report->artistId != null) {

			$stmt->bindValue('artist', $report->labelId);
		}

		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}

		$dates = array();
		while ($row = $stmt->fetchObject()) {

			$dates[] = $row->quarter;
		}
		
		return $dates;
	}

	public function getSalesReportByArtist($labelId = null, $artistId = null) {

		$sql = "SELECT CONCAT(a.name, ' ', s.format) AS position,
				SUM(s.quantity) AS num,
				SUM(s.royalty) * r.deal AS value
			FROM sales s
			INNER JOIN artists a ON a.id = s.artist_id
			INNER JOIN releases r ON r.id = s.release_id
			WHERE s.invoiced = 0";
			
		if (null !== $artistId) {
		
			$sql .= " AND a.id = :artistId";
		}
			
		$sql .= " GROUP BY s.artist_id, s.format";
		$stmt = $this->dbh->prepare($sql);
		if (null !== $artistId) {
		
			$stmt->bindValue('artistId', $artistId);
		}
		
		if (!$stmt->execute()) {
	
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$report = (object)array(
		
			'date' => null,
			'items' => array(),
			'labelId' => $labelId,
			'artistId' => $artistId,
		);
		
		$dates = $this->getDatesForReport($report);
		$report->date = implode(', ', $dates);
		
		while ($row = $stmt->fetchObject()) {

			$report->items[] = $row;
		}
		
		return $report;
	}
	
	public function getSweepstake($key) {
	
		switch ($key) {
		
			case 'psy-force':
				return array(
					'metaTitle' => "Gewinne freien Eintritt für die Psy-Forge / Spekta's Birthday",
					'facebookDescription' => "Für den 24. Januar in der Klangstation gibt es 5 Mal freien Eintritt zu gewinnen! Erlebe internationale Top Acts wie Necmi, Nitro & Glycerine, Nayana She, Spectral Vibration, Spekta, Maskay und HighQ.",
					'facebookImage' => "http://www.3886records.de/img/psy-forge.jpg",
				);
				
			case 'labelnight-no2':
				return (object)array(
					'party' => 'labelnight-no2',
					'eventName' => 'Goa Cologne Birthday / 3886records Labelnight No.2',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '5 Mal freier Eintritt für die Labelnight am 21.3.2014 zu gewinnen!',
					'validUntil' => '2014-03-20 23:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Labelnight No.2 / Goa Cologne Birthday',
					'description' => 'Für den 21. März in der Werkstatt Köln gibt es 5 Mal freien Eintritt zu gewinnen! Erlebe internationale 3886records Acts sowie ein Top Local Lineup.',
					'image' => 'http://www.3886records.de/img/flyer/labelnight-2014-03.jpg',
					'facebookLink' => 'http://www.facebook.com/events/560921743997008/',
				);
				
			case 'darkn8en':
				return (object)array(
					'party' => 'darkn8en',
					'eventName' => 'Darkn8en - Dark Psy Goa',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Darkn8en am 5.4.2014 zu gewinnen!',
					'validUntil' => '2014-04-04 23:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Darkn8en',
					'description' => 'Für den 5. April in der N8Lounge Bonn gibt es 3 Mal freien Eintritt zu gewinnen! Ein Muss für Fans dunkler Materie.',
					'image' => 'http://www.3886records.de/img/flyer/darkn8en.jpg',
					'facebookLink' => 'https://www.facebook.com/events/240201426146009/?fref=gewinnspiel',
					'winners' => array(
						'Jules Vegas',
						'Zerstreutes Kleingeld',
						'Nicole Ananda',
					),
				);
				
			case 'psy-spring-night':
				return (object)array(
					'party' => 'psy-spring-night',
					'eventName' => 'Psychedelic Spring Night',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Psychedelic Spring Night',
					'validUntil' => '2014-04-10 23:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Psychedelic Spring Night',
					'description' => 'Für den 11. April in der N8Lounge Bonn gibt es 3 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Top Acts',
					'image' => 'http://www.3886records.de/img/flyer/spring-night.jpg',
					'facebookLink' => 'https://www.facebook.com/events/599409263459218/?fref=gewinnspiel',
					'winners' => array(
						'Mario Hilberath',
						'Sebastian Van Mil',
						'Dominic Fox',
					),
				);
				
			case 'lustig-stampfen':
				return (object)array(
					'party' => 'lustig-stampfen',
					'eventName' => 'Lustig Stampfen - Prog, Psy & Dark Goa',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '5 Mal freier Eintritt für die Lustig Stampfen am 26.4.2014 zu gewinnen!',
					'validUntil' => '2014-04-25 23:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Lustig Stampfen',
					'description' => 'Für den 26. April in der Werkstatt Köln gibt es 5 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Top Acts',
					'image' => 'http://www.3886records.de/img/flyer/lustig-stampfen.jpg',
					'facebookLink' => 'https://www.facebook.com/events/726505837382375/?fref=gewinnspiel',
					'winners' => array(
						'Melanie Bürger',
						'Felix Larisika',
						'Thomas Langer',
						'Enny Mol',
						'Claudia Jahn',
					),
				);
				
			case 'corpus-sarasvati':
				return (object)array(
					'party' => 'corpus-sarasvati',
					'eventName' => 'Corpus Sarasvati (Goa, Psy & Prog)',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Corpus Sarasvati am 02.05.2014 zu gewinnen!',
					'validUntil' => '2014-05-02 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Corpus Sarasvati',
					'description' => 'Für den 2. Mai in der N8Lounge gibt es 3 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Top Acts',
					'image' => 'http://www.3886records.de/img/flyer/corpus-sarasvati.jpg',
					'facebookLink' => 'https://www.facebook.com/events/223432371180723/?fref=gewinnspiel',
					'winners' => array(),
				);
				
			case '3886inlove':
				return (object)array(
					'party' => '3886inlove',
					'eventName' => '3886 in Love',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die 3886 in Love am 05.07.2014 zu gewinnen!',
					'validUntil' => '2014-07-04 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die 3886 in Love',
					'description' => 'Für den 5. Juli in der N8Lounge gibt es 3 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Top Acts',
					'image' => 'http://www.3886records.de/img/flyer/3886inlove.jpg',
					'facebookLink' => 'https://www.facebook.com/events/612196595516881/?fref=gewinnspiel',
					'winners' => array(
						'Lena Faye',
						'André Di Lauro',
						'Sarah Wochnik',
					),
				);
					
			case 'mahadeva':
				return (object)array(
					'party' => 'mahadeva',
					'eventName' => 'Mahadeva Linga',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Mahadeva Linga am 24.10.2014 zu gewinnen!',
					'validUntil' => '2014-10-23 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Mahadeva Linga',
					'description' => 'Für den 24. Oktober in der N8Lounge gibt es 3 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Label DJs',
					'image' => 'http://www.3886records.de/img/flyer/mahadeva-linga.jpg',
					'facebookLink' => 'https://www.facebook.com/events/262469157273772/?fref=gewinnspiel',
					'winners' => array(
						'Andre Lorenz',
						'Louisa Käsi',
						'Martin Urner',
					),
				);

			case 'garuda':
				return (object)array(
					'party' => 'garuda',
					'eventName' => 'Wings of Garuda',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Wings of Garuda am 14.11.2014 zu gewinnen!',
					'validUntil' => '2014-11-13 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Wings of Garuda',
					'description' => 'Für den 14.11. in der N8Lounge gibt es 3 Mal freien Eintritt zu gewinnen!',
					'image' => 'http://www.3886records.de/img/flyer/garuda.jpg',
					'facebookLink' => 'https://www.facebook.com/events/262469157273772/?fref=gewinnspiel',
				);

			case 'brahma-quinio':
				return (object)array(
					'party' => 'brahma-quinio',
					'eventName' => 'Brahma Quinio',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '5 Mal freier Eintritt für die Brahma Quinio 12.12.2014 zu gewinnen!',
					'validUntil' => '2014-12-11 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Wings of Garuda',
					'description' => 'Da wir bei der Wings of Garuda leider versäumt haben die Gewinner zu ziehen, gibt es nun 5 Mal freien Eintritt für die Brahma Quinio zu gewinnen!',
					'image' => 'http://www.3886records.de/img/flyer/brahma-quinio.jpg',
					'facebookLink' => 'https://www.facebook.com/events/753095014733588/?fref=gewinnspiel',
					'winners' => array(
						'Donna Kopf',
						'Gianluca Keßler',
						'Manzur Kadiri',
						'Tanja Kuhn',
						'Vittorio David R',
					),
				);

			case 'psyforge2':
				return (object)array(
					'party' => 'psyforge2',
					'eventName' => 'Psy-Forge 2',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für den 30.1.2015 zu gewinnen!',
					'validUntil' => '2015-01-29 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Psy-Forge 2',
					'description' => 'Für den 30.1.2015 gibt es drei Mal freien Eintritt zu gewinnen.',
					'image' => 'http://www.3886records.de/img/flyer/brahma-quinio.jpg',
					'facebookLink' => 'https://www.facebook.com/events/511261482310857//?fref=gewinnspiel',
					'winners' => array(),
				);

			case 'mahadeva2015':
				return (object)array(
					'party' => 'mahadeva2015',
					'eventName' => 'Mahadeva Linga',
					'headline' => 'Gewinne freien Eintritt!',
					'info' => '3 Mal freier Eintritt für die Mahadeva Linga am 2.5.2015 zu gewinnen!',
					'validUntil' => '2015-05-01 12:00:00',
					'metaTitle' => 'Gewinne freien Eintritt für die Mahadeva Linga',
					'description' => 'Für den 2. Mai in der N8Lounge gibt es 3 Mal freien Eintritt zu gewinnen! Erlebe Progressive & Psychedelic Label DJs',
					'image' => 'http://www.3886records.de/img/flyer/mahadeva-linga-2015.jpg',
					'facebookLink' => 'https://www.facebook.com/events/799924283403679/?fref=gewinnspiel',
					'winners' => array(
						'Gianluca Keßler',
						'Hanna Von Hell',
						'Sandra Landa',
					),
				);
		}
	}
}
