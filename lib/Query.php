<?php

class Query
{
	protected static $_dbh;

	protected $select;
	protected $model;
	protected $leftJoins = array();
	protected $alias;
	protected $where;
	protected $params = array();
	protected $joins = array();

	public static function setDbh($dbh) {
	
		self::$_dbh = $dbh;
	}
	
	public function select($select = null) {
	
		$this->select = $select;
		return $this;
	}
	
	public function from($model, $alias = null) {
	
		$this->model = $model;
		$this->alias = $alias;
		return $this;
	}
	
	public function where($where) {
	
		$this->where = $where;
		return $this;
	}
	
	public function leftJoin($assoc, $alias) {
	
		$this->leftJoins[$alias] = $assoc;
		return $this;
	}
	
	public function setParam($key, $value) {
	
		$this->params[$key] = $value;
		return $this;
	}
	
	public function execute() {
	
		$className = $this->model;
		
		$reflect = new ReflectionClass($className);
		$properties = $reflect->getProperties(ReflectionProperty::IS_PROTECTED);
		
		$fields = array();
		foreach ($properties as $property) {

			if (array_key_exists($property->name, $className::$associations)) continue;
			$fields[] = '`'. $this->alias .'`.`'. $property->name . '` AS `'. $this->alias .'_'. $property->name .'`' ;
		}
		
		$classes = array(
			$this->alias => $className,
		);


		// select
		// from events e
		// join e.eventArtists ea
		// join ea.artists a
		
		$joins = array();
		foreach ($this->leftJoins as $joinTableAlias => $join) {
		
			if (preg_match('/^(.+)\.(.+)$/', $join, $matches)) {
			
				$joinFromAlias = $matches[1];
				$joinToFieldName = $matches[2];

				if (isset($classes[$joinFromAlias]::$associations[$joinToFieldName])) {
				
					$association = $classes[$joinFromAlias]::$associations[$joinToFieldName];
					$joinClass = $association['class'];
					$reflectJoin = new ReflectionClass($joinClass);
					$joinProperties = $reflectJoin->getProperties(ReflectionProperty::IS_PROTECTED);
					
					$this->joins[] = (object)array(
						'alias' => $joinTableAlias,
						'fromAlias' => $joinFromAlias,
						'toFieldName' => $joinToFieldName,
						'properties' => $joinProperties,
						'assoc' => $association
					);
					
					$classes[$joinFromAlias] = $association['class'];
					
					foreach ($joinProperties as $property) {

						if (array_key_exists($property->name, $className::$associations)) continue;
						$fields[] = '`'. $joinTableAlias .'`.`'. $property->name . '` AS `'. $joinTableAlias .'_'. $property->name .'`' ;
					}
					
					$joins[] = ' LEFT JOIN `'. $joinClass::$tableName .'` `'. $joinTableAlias .'` ON `'. $joinTableAlias .'`.`'. $association['to'] .'` = `'. $this->alias .'`.`'. $association['from'] .'`';
				}
			}
		}
		
		$sql = 'SELECT '. implode(',', $fields) . ' FROM `'. $className::$tableName . '` `'. $this->alias .'`';
		foreach ($joins as $join) {
		
			$sql .= $join;
		}
		
		if (!empty($this->where)) {
		
			$sql .= ' WHERE '. $this->where;
		}
		
		$stmt = self::$_dbh->prepare($sql);
		foreach ($this->params as $key => $value) {
		
			$stmt->bindValue($key, $value);
		}
		
		if (!$stmt->execute()) {
		
			$errorInfo = $stmt->errorInfo();
			throw new \Exception($errorInfo[2], $errorInfo[1]);
		}
		
		$object = null;
		while ($raw = $stmt->fetchObject()) {

			if (null === $object) {
			
				$object = new $className;
				foreach ($raw as $property => $value) {
				
					if (preg_match('/^'. $this->alias .'_(.+)$/', $property, $matches)) {
					
						$object->{$matches[1]} = $value;
					}
				}
			}
			
			foreach ($this->joins as $join) {
			
				$joinClass = $join->assoc['class'];
				$joinObject = new $joinClass;
				foreach ($raw as $property => $value) {
				
					if (preg_match('/^'. $join->alias .'_(.+)$/', $property, $matches)) {
					
						$joinObject->{$matches[1]} = $value;
					}
				}
				
				$joinValues = $object->{$join->toFieldName};
				$joinValues[] = $joinObject;
				
				// TODO: find proper target object this joined data belongs to (deep assocs)
				$object->{$join->toFieldName} = $joinValues;
			}
		}
		
		return $object;
	}
}