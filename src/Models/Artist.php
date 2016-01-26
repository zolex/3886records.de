<?php

namespace Models;

class Artist extends AbstractModel
{
	const TYPE_PRODUCER = 0;
	const TYPE_DJ = 1;

	protected $id;
	protected $label;
	protected $label_id;
	protected $genre_id;
	protected $crews = array();
	protected $members = array();
	protected $links = array();
	protected $videos = array();
	protected $events = array();
	protected $releases = array();
	protected $genres = array();
	protected $type;
	protected $key;
	protected $is_dj;
	protected $is_producer;
	protected $is_liveact;
	protected $position;
	protected $name;
	protected $lw_name;
	protected $shortInfo;
	protected $longInfo;
	protected $soundcloud;
	protected $mixcloud;
	protected $firstname;
	protected $lastname;
	protected $location;
	protected $birthday;
	protected $visible;
	protected $email;
}