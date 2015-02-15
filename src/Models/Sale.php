<?php

namespace Models;

class Sale extends AbstractModel
{
	public static $tableName = 'sales';
	
	protected $id;
	protected $report_id;
	protected $artist_id;
	protected $label_id;
	protected $release_id;
	protected $release_artist;
	protected $release_name;
	protected $track_artist;
	protected $track_title;
	protected $mix_name;
	protected $format;
	protected $sale_type;
	protected $quantity;
	protected $value;
	protected $deal;
	protected $royalty;
	protected $isrc;
	protected $ean;
	protected $store;
	protected $track_ref;
	protected $invoiced;
	protected $avail;
}