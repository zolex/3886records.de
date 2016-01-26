<?php

namespace Domains;

class Artists extends AbstractDomain {

	protected $_correlationMap = array(

		'releases' => 'Release',
		'crews' => 'artists',
		'members' => 'artists',
		'links' => 'artistLinks',
		'videos' => 'artistVideos',
		'events' => 'Event',
		'genres' => 'Genre',
	);

	public function findOneByKey($key) {

		$query = "SELECT a.*,
					c.id AS crew_id,
					c.key AS crew_key,
					c.name AS crew_name,
					cm.id AS member_id,
					cm.name AS member_name,
					cm.key AS member_key,
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
					e.facebook AS event_facebook,
					g.id AS genre_id,
					g.key AS genre_key,
					g.name AS genre_name,
					ar.id AS release_id,
					ar.title AS release_title,
					ar.cover AS release_cover,
					ar.beatport AS release_beatport,
					ar.date AS release_date
			FROM artists a
			LEFT JOIN artist_crews ac ON ac.artist_id_original = a.id
			LEFT JOIN releases ar ON (ar.artist_id = a.id AND ar.visible = 1)
			LEFT JOIN artists c ON c.id = ac.artist_id_crew
			LEFT JOIN artist_crews acm ON acm.artist_id_crew = a.id
			LEFT JOIN artists cm ON cm.id = acm.artist_id_original
			LEFT JOIN artist_links l ON l.artist_id = a.id
			LEFT JOIN artist_genres ag ON ag.artist_id = a.id
			LEFT JOIN genres g ON g.id = ag.genre_id
			LEFT JOIN artist_videos v ON v.artist_id = a.id
			LEFT JOIN event_artists ea ON ea.artist_id = a.id
			LEFT JOIN events e ON e.id = ea.event_id AND e.fromTime >= NOW()
			WHERE a.`key` = :key
			ORDER BY e.fromTime ASC";

		return $this->fetchWithCorrelations($query, array('key' => $key));
	}
}