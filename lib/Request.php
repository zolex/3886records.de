<?php

class Request
{
    protected $params = array();

	public function __construct() {
	
		$this->params = $_GET;
	}
	
    public function isXmlHttpRequest() {
    
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
          $_SERVER['HTTP_X_REQUESTED_WITH']  == 'XMLHttpRequest';
    }
    
	public function isPost() {
	
		return isset($_POST) && 0 !== count($_POST);
	}
	
    public function getPost($key = null, $default = null) {
    
        if (null === $key) {
        
            return $_POST;
            
        } else if (isset($_POST[$key])) {
        
            return $_POST[$key];
            
        } else {
        
            return $default;
        }
    }
    
    public function getRoute() {
    
		if (preg_match('@^(/[^\?]*)@', $_SERVER['REQUEST_URI'], $matches)) {
		
			return strtolower($matches[1]);
			
		} else {
		
			return false;
		}
    }
    
    public function setParam($key, $value) {
    
        $this->params[$key] = $value;
    }
    
    public function getParam($key) {
    
        if (!isset($this->params[$key])) {
        
            return null;
        }
        
        return $this->params[$key];
    }
	
	public function getParams() {

		return $this->params;
	}
}
