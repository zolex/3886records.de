<?php

namespace Models;

class AbstractModel
{
	public static $_tableName;
	public static $_associations;

	public function __set($property, $value) {
	
		if (!property_exists($this, $property)) {
		
			throw new \Exception("The model '". get_class($this) . "' does not have a property '". $property ."'");
		}
		
		if ('_' == substr($property, 0, 1)) {
		
			throw new \Exception("Not allowed to set '". $property ."' on '". get_class($this) . "'");
		}
		
		$this->$property = $value;
		return $this;
	}
	
	public function __isset($property) {
	
		if (!property_exists($this, $property) || null === $this->$property) {
		
			return false;
		}

		return true;
	}
	
	public function &__get($property) {
	
		if (!property_exists($this, $property)) {
		
			throw new \Exception("The model '". get_class($this) . "' does not have a property '". $property ."'");
		}
		
		return $this->$property;
	}

	public function fromArray($array) {

		return $this->fromObject($array);
	}

	public function fromObject($object, $prefix = null) {

		if (!is_object($object) && !is_array($object)) {

			throw new \Exception('parameter is not an object or an array');
		}

		foreach ($object as $property => $value) {

			if (null !== $prefix) {

				$property = preg_replace('/^'. $prefix .'/', '', $property);
			}

			if (property_exists($this, $property)) {

				$this->$property = $value;
			}
		}

		return $this;
	}
}