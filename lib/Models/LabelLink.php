<?php

namespace Models;

class LabelLink extends AbstractModel
{
	public static $tableName = 'label_links';
	
	protected $id;
	protected $label_id;
	protected $type;
	protected $link;
}