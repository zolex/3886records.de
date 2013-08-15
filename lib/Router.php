<?php

class Router
{
    public function route(Request $request) {
    
        $result = array();
        $currentRoute = $request->getRoute();
        foreach ($request->getConfig('routes') as $route => $config) {
        
            $template = $config['template'];
            if (preg_match('#'. $route .'#i', $currentRoute, $matches)) {
            
                foreach ($matches as $index => $value) {
                
                    if (isset($config['args'][$index])) {
                    
                        $request->setParam($config['args'][$index], $value);
                        
                    } else {
                    
                        $request->setParam($index, $value);
                    }
                }
                
                if (isset($config['controller'])) {
                
					$dispatched = false;
                    if (is_array($config['controller']) && 2 == count($config['controller'])) {
					
						$className = $config['controller'][0];
						$controller = new $className;
						
						if (method_exists($controller, $config['controller'][1])) {
						
							$action = $config['controller'][1];
							$result = $controller->$action($request);
							$dispatched = true;
						}
					}
					
					if (!$dispatched) {
					
						$result = $config['controller']($request);
					}
					
					if (false === $result) {

                        $template = '404';
                        $result = array(
                            'route' => $request->getRoute(),
                        );
                    }
                }
                
                return array($template, $result);
            }
        }
        
        return null;
    }
}
