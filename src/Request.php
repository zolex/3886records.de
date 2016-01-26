<?php

class Request
{
    protected $params = array();
    protected $config = array();

	public function __construct($config = array()) {
	
        $this->config = $config;
		$this->params = $_GET;
	}
	
    public function getConfig($index = null) {

        if (null === $index) {
        
            return $this->config;

        } else if (array_key_exists($index, $this->config)) {

            return $this->config[$index];
        
        } else {

            return null;
        }
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
	
	public function get($key = null, $default = null) {
    
        if (null === $key) {
        
            return $_GET;
            
        } else if (isset($_GET[$key])) {
        
            return $_GET[$key];
            
        } else {
        
            return $default;
        }
    }
    
    public function getRoute() {
    
		if (preg_match('@^(/[^\?]*)@', $_SERVER['REQUEST_URI'], $matches)) {
		
			return $matches[1];
			
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
