<?php

namespace Models;

class SalesReport extends AbstractModel
{
	public static $tableName = 'labels';
	
	protected $id;
	protected $sales = array();
	protected $name;
	protected $filename;
	protected $quarter;
	protected $created_at;
}