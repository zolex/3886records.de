<?php

namespace Models;

class Artist extends AbstractModel
{
	protected $id;
	protected $label;
	protected $label_id;
	protected $links = array();
	protected $videos = array();
	protected $events = array();
	protected $key;
	protected $position;
	protected $name;
	protected $shortInfo;
	protected $longInfo;
	protected $soundcloud;
	protected $firstname;
	protected $lastname;
	protected $location;
	protected $birthday;
}