<?php

class ViewLoader
{
    public static function load($name, $params = array()) {
    
        
        $script = 'views/'. $name . '.phtml';
        if (!is_file($script) || !is_readable($script)) {
        
            throw new Exception('Could not load view "'. $script .'"');
        }
        
        ob_start();
        extract($params);
        require $script;
        return ob_get_clean();
    }
}
