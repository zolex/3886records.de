<?php

namespace Models;

class Subscription extends AbstractModel
{
	protected $id;
	protected $genres = array();
	protected $email;
	protected $firstname;
	protected $lastname;
	protected $alias;
	protected $newsletter = 1;
	protected $promotions = 1;
	protected $active = 0;
	protected $hash;
}