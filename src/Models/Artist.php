<?php

namespace Models;

class Artist extends AbstractModel
{
	const TYPE_PRODUCER = 0;
	const TYPE_DJ = 1;

	protected $id;
	protected $label;
	protected $label_id;
	protected $links = array();
	protected $videos = array();
	protected $events = array();
	protected $releases = array();
	protected $type;
	protected $key;
	protected $position;
	protected $name;
	protected $shortInfo;
	protected $longInfo;
	protected $soundcloud;
	protected $mixcloud;
	protected $firstname;
	protected $lastname;
	protected $location;
	protected $birthday;
	protected $visible;
}