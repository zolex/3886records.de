<?php

namespace Models;

class Label extends AbstractModel
{
	public static $tableName = 'labels';
	public static $associations = array(
		'links' => array(
			'type' => 'oneToMany',
			'class' => '\Models\LabelLink',
			'from' => 'id',
			'to' => 'label_id'
		),
	);
	
	protected $id;
	protected $key;
	protected $name;
	protected $shortInfo;
	protected $longInfo;
	protected $soundcloud;
	protected $address;
	protected $phone;
	protected $email;
	protected $links;
}