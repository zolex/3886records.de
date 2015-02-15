<?php

namespace Models;

class Event extends AbstractModel
{
	const UPCOMMING = 1;
	const PAST = 2;

	protected $id;
	protected $artists = array();
	protected $key;
	protected $name;
	protected $shortInfo;
	protected $longInfo;
	protected $fromTime;
	protected $toTime;
	protected $flyer;
	protected $facebook;
	protected $visible;

	public function toJson() {

		return json_encode((object)array(
			'id' => $this->id,
			'title' => $this->name,
			'start' => $this->fromTime,
			'end' => $this->toTime,
		));
	}
}