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
	protected $usertype;
	protected $newsletter = 1;
	protected $promotions = 1;
	protected $active = 0;
	protected $hash;
	
	public function getName() {
	
		if (!empty($this->alias)) {
		
			return $this->alias;
		}
		
		if (!empty($this->firstname)) {
		
			if (!empty($this->lastname)) {
			
				return $this->firstname .' '. $this->lastname;
				
			} else {
			
				return $this->firstname;
			}
		}
		
		return null;
	}
}
