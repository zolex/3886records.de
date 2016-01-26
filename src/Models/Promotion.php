<?php

namespace Models;

class Promotion extends AbstractModel
{
	protected $id;
	protected $key;
	protected $release;
	protected $tracks = array();
	protected $genre;
	protected $title;
	protected $banner;
	protected $banner2;
	protected $text1;
	protected $head2;
	protected $text2;
	protected $image2;
	protected $head3;
	protected $text3;
	protected $image3;
	protected $visible;
	protected $widget;
	protected $download;
	protected $valid_until;
	
	public function isExpired() {
	
		return $this->valid_until !== null && strtotime($this->valid_until) < time();
	}
}
