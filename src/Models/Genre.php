<?php

namespace Models;

class Genre extends AbstractModel
{
	protected $id;
	protected $subgenres = array();
	protected $superiors = array();
	protected $artists = array();
	protected $parent_id;
	protected $key;
	protected $position;
	protected $name;
	protected $altName;
	protected $description;
	protected $soundcloud;
}