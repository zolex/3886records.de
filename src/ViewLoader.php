<?php

class ViewLoader
{
    protected static $dir = 'views';

    public static function setDir($dir) {

        self::$dir = $dir;
    }

    public static function load($name, $params = array()) {
    
        $script = self::$dir . DIRECTORY_SEPARATOR . $name . '.phtml';
        if (!is_file($script) || !is_readable($script)) {
        
            throw new Exception('Could not load view "'. $script .'"');
        }
        
        ob_start();
        extract($params);
        require $script;
        return ob_get_clean();
    }
}
