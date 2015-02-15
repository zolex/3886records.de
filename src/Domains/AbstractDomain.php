<?php

namespace Domains;

abstract class AbstractDomain {

	private $_dbh;

	protected static $__instance;

	protected $_modelName;

	protected $_correlationMap = array();

	protected function __construct() {
		
		$this->_determineModelName();
		$this->_determineCorrelations();
		$this->_buildCorrelationAliases();
	}

	public static function getInstance() {

		if (null === static::$__instance) {

			static::$__instance = new static;
		}

		return static::$__instance;
	}

	public function setDbh(\PDO $dbh) {

		$this->_dbh = $dbh;
		return $this;
	}
	
	public function getDbh() {
	
		return $this->_dbh;
	}

	protected function _execQuery($query, array $params = array()) {

		$stmt = $this->getDbh()->prepare($query);
		foreach($params as $key => $value) {

			$stmt->bindValue($key, $value);
		}

		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}

		return $stmt;
	}

	public function fetchWithCorrelations($query, array $params = array()) {

		$stmt = $this->_execQuery($query, $params);
		
		$entity = null;
		while ($row = $stmt->fetchObject()) {

			if (null === $entity) {

				$entity = (new $this->_modelName)->fromObject($row, null, true);
			}

			foreach ($this->_correlationMap as $config) {

				$primary = $config['prefix'] . $config['primary'];
				if (null !== $row->$primary && !array_key_exists($row->$primary, $entity->{$config['property']})) {

					$entity->{$config['property']}[$row->$primary] = (new $config['class'])->fromObject($row, $config['prefix']);
				}
			}
		}

		return $entity;
	}

	protected function _determineCorrelations() {

		foreach ($this as $member => $value) {

			if (false === strpos($member, '_') && is_array($this->$member) && !isset($this->_correlationMap[$member])) {

				$this->_addDetaultCorrelation($member);
				$entityName = preg_replace('/s$/', '', $member);
				$this->_correlationMap[$member] = array(
					'primary' => 'id',
					'class' => '\\Models\\'. ucfirst($entityName),
					'prefix' => strtolower($entityName) . '_',
					'property' => $member,
				);
			}
		}
	}

	protected function _buildCorrelationAliases() {

		foreach ($this->_correlationMap as $member => $alias) {

			if (is_string($alias)) {

				$entityName = preg_replace('/s$/', '', $alias);
				$this->_correlationMap[$member] = array(
					'primary' => 'id',
					'class' => '\\Models\\'. ucfirst($entityName),
					'prefix' => strtolower(preg_replace('/s$/', '', $member)) . '_',
					'property' => $member,
				);
			}
		}
	}

	protected function _determineModelName() {

		if (null === $this->_modelName) {

			$this->_modelName = '\\Models\\'. substr(ucfirst(preg_replace('/s$/', '', get_class($this))), 8);
		}
	}
}