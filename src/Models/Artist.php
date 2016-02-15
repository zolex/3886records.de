<?php

namespace Models;

class Artist extends AbstractModel
{
	const TYPE_PRODUCER = 0;
	const TYPE_DJ = 1;

	protected $id;
	protected $key;
	
	protected $label; // old ?
	protected $genre_id; // old?

	protected $label_id;
	protected $crews = array();
	protected $members = array();
	protected $links = array();
	protected $videos = array();
	protected $events = array();
	protected $releases = array();
	protected $genres = array();
	
	protected $type; // old: 0 = artist, 1 = DJ
	
	
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
	protected $location;
	protected $birthday;

	protected $firstname;
	protected $lastname;
	protected $street;
	protected $zip;
	protected $city;
	protected $country;
	
	protected $payment_type;
	protected $payment_paypal;
	protected $payment_name;
	protected $payment_iban;
	protected $payment_bic;

	protected $fee_dj_desired;
	protected $fee_dj_min;
	protected $fee_live_desired;
	protected $fee_live_min;

	protected $email;
	protected $phone;
	protected $fbprofile;

	protected $visible;
}