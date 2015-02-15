<?php

namespace Models;

class Release extends AbstractModel
{
	protected $id;
	protected $links = array();
	protected $catalog;
	protected $artist;
	protected $genre;
	protected $title;
	protected $cover;
	protected $date;
	protected $format;
	protected $type;
	protected $deal;
	protected $visible;
}