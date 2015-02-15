<?php

/**
 * @package Models
 * @subpackage 
 * @author Andreas Linden <zlx@gmx.de>
 * @author <you...?>
 * @magic May the force be with you, my padawan!
 * @date 2014-11-26
 *
 */

namespace Models;

/**
 * A quite basic abstract data entity, so called model.
 * Designed for quick replacement of your old code while
 * trying to create, encourage and keep a proper object-
 * oriented access on the underling database relation.
 *
 * An inmportant item of our force is realized using the
 * magic concept to give public access to protected and
 * private member variables. Also we magically implement
 * getter and setter methods in camelCase style.
 *
 * These featues should easily allow updating your project
 * step-by-step and finally use a seriously object oriented
 * mapper like Doctrine.
*/
class AbstractModel {

	/**
	 * By default we do not allow public access to our members!
	 * This may be enabled temporarily to allow backwards
	 * compatibility when working with legacy code (Don't be lazy
	 * and go update that old piece of code right now!)
	 *
	 * It's mainly meant for lowering the barriers of just using
	 * this neat model calss as soon as possible but also for lazy
	 * people working on small projects.
	 */
	protected static $_allowPublicAccess = false;

	/*
	 * When working with legacy code you may just finally want
	 * use this object-relational-mapper, you can map your legacy
	 * variable names to better ones or ones matching your new
	 * conventions or even beeing required by your targeted ORM.
	 *
	 * @example $user->legacy_variable_Name;
	 * @example $user->newVariableName;
     * 
	 * If a flat array of keys and values is defined, the key
	 * will be an alias for the variable with the name of the
	 * related value. Affecting public access of variables 
	 * and existance of getters and setters.
	 *
	 * @example array('legacy_variable_Name' => 'newVariableName');
	 */
	protected static $_memberMapping = array();

	/**
	 * Enable the member-mapping feature an optionally pass the map
	 *
	 * @param array $map The optional map
	 * @return void
	 */
	public static function enableMemberMapping(array $map = null) {

		if (null !== $mapping) {

			self::setMemberMapping($mapping);
		}

		self::$_memberMappingEnabled = true;
	}

	/**
	 * Disable the member-mapping feature during runtime. This can
	 * may useful to see if a part of your code fully complies with
	 * your new naming of model-members.
	 *
	 * @return void
	 */
	public static function disableMemberMapping() {

		self::$_memberMappingEnabled = false;
	}

	/**
	 * Set the map during runtime insread of defining it in the derived
	 * model class. could be useful for partial compliance of code-parts.
	 *
	 * @param array $map The new map to use
	 * @return void
	 */
	public static function setMemberMapping(array $map) {

		self::$_memberMapping = $map;
	}

	public static function enablePublicAccess() {

		self::$_allowPublicAccess = true;
	}

	/**
	 * We need this in our concept, especially for checking
	 * of mapped and private members beeing set.
	 *
	 * @param string $property The member to set the value for
	 * @param mixed $value The value to set on the member
	 * @return AbstractModel
	 * @magic
	 */
	public function __isset($property) {
	
		if (!property_exists($this, $property)) {

			if (isset(self::$_memberMapping[$property]) && property_exists($this, self::$_memberMapping[$property])) {

				$property = self::$_memberMapping[$property];

			} else {

				return false;
			}
		}

		return null !== $this->$property;
	}

	/**
	 * This is where the write access magic is done for protected
	 * and private model proeprties as well as for mapped members.
	 * Also this may be required to not trigger notices in some php
	 * versions.
	 *
	 * @param string $property The member to set the value for
	 * @param mixed $value The value to set on the member
	 * @return AbstractModel
	 * @magic
	 */
	public function __set($property, $value) {
	
		$property = $this->_filterAndValidatePropertyName($property);
		if ('_' == substr($property, 0, 1)) {
		
			throw new \Exception("Can not set the internal member '". $property ."' on '". get_class($this) . "'");
		}
		
		$this->$property = $value;
		return $this;
	}
	
	/**
	 * Magically implements public read access for member variables.
	 * 
	 * Explicitly omits the checking of members starting with an
	 * underscore, because we don't care if you read these and
	 * it's faster to not check it for each access of any member.
	 *
	 * @throws \Exception When you are not writing good code :P
	 * @param string $property Name of the model member to read
	 * @return mixed The value of the member with the given name
	 * @magic
	 */
	public function &__get($property) {
	
		$property = $this->_filterAndValidatePropertyName($property);
		return $this->$property;
	}

	/**
	 * Fast magic setter and getter implementation.
	 *
	 * @param string $method name of the called method
	 * @param array $args params of the called method
	 * @return mixed
	 */
	public function __call($method, $args) {

		if (preg_match('/^(s|g)et([\w\d]+)/', $method, $matches)) {

			$property = $this->_filterAndValidatePropertyName(lcfirst($matches[2]), false);
			if ('s' === $matches[1]) {

				$this->$property = $args[0];
				return $this;
			
			} else if ('g' === $matches[1]) {

				return $this->$property;
			}
		}
	}

	/**
	 * Set the values for multiple model-mebmers at once by passing
	 * a simple object containing the data in proeprties matching the
	 * model-member names.
	 *
	 * @param object $object With data for the model-members
	 * @param string $prefix Optionally only use variables with this prefix and cut it off for the final name
	 * @param bool $ingoreOtherParams Ignore params having a prefix or not (depending on existance of a preset)
	 *
	 * The optional params are primarily used for object-oriented
	 * mapping of the underlying database relation's correlations.
	 * @see $this->mapCorrelation(...)
     *
	 * @return self
	 */
	public function fromObject(\stdClass $object, $prefix = null, $ingoreOtherParams = true) {

		foreach ($object as $property => $value) {

			if (true === $ingoreOtherParams) {

				if (null === $prefix && false !== strpos($property, '_')) continue;
				if (null !== $prefix && 0 !== strpos($property, $prefix)) continue;
			}

			if (null !== $prefix) {

				$property = preg_replace('/^'. $prefix .'/', '', $property);
			}

			$property = $this->_filterAndValidatePropertyName($property);
			$this->$property = $value;
		}

		return $this;
	}

	/**
	 * Get the values of the entity at once.
	 *
	 * @param string $prefix Optionally only use variables with this prefix and cut it off for the final name
	 * @param bool $ingoreOtherParams Ignore params having a prefix or not (depending on existance of a preset)
	 * @return self
	 */
	public function toObject($prefix = null, $ingoreOtherParams = true) {

		$object = new \stdClass;
		foreach ($this as $property => $value) {

			if (true === $ingoreOtherParams) {

				if (null === $prefix && false !== strpos($property, '_')) continue;
				if (null !== $prefix && 0 !== strpos($property, $prefix)) continue;
			}

			if (null !== $prefix) {

				$property = preg_replace('/^'. $prefix .'/', '', $property);
			}

			$object->$property = $this->$property;
		}

		return $object;
	}

	/**
	 * Json representation of the entity
	 *
	 * @return string
	 */
	public function toJson() {

		return json_encode($this->toObject());
	}

	public function __toString() {

		return $this->toJson();
	}

	/**
	 * Array interface for the fromObject() method
	 *
	 * @param array $array With data for the model-members
	 * @return self
	 */
	public function fromArray(array $array) {

		return $this->fromObject((object) $array);
	}

	/**
	 * Handles the feature to enable public access of mebebers on
	 * specific models or globally when setting on the AcstractModel.
	 * Additionally handles the mapping of variable names.
	 * 
	 * @param string $property Requested property name
	 * @return string Property name which may have been mapped
	 * @throws \Exception when wrinting bad code :P
	 */
	protected function _filterAndValidatePropertyName($property, $forPublicAccess = true) {

		if ($forPublicAccess && !(self::$_allowPublicAccess || static::$_allowPublicAccess)) {

			throw new \Exception("No public access on private and protected members, fool!");
		}

		if (!property_exists($this, $property)) {

			if (isset(self::$_memberMapping[$property]) && property_exists($this, self::$_memberMapping[$property])) {

				// TODO: trigger a notice for using a mapped variable name
				$property = self::$_memberMapping[$property];

			} else {
		
				throw new \Exception("The model '". get_class($this) . "' does not have a property '". $property ."'");
			}
		}

		return $property;
	}
}