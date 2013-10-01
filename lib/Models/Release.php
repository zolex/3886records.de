<?php

namespace Models;

class Release extends AbstractModel
{
	protected $id;
	protected $links = array();
	protected $artist;
	protected $genre;
	protected $title;
	protected $cover;
	protected $date;
	protected $format;
	protected $type;
}