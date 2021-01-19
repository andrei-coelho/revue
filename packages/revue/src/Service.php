<?php 

namespace Revue\src;

abstract class Service {

    public static function call($class, $method, $vars = []){
        
        $obj = new $class();
        self::create($obj, $vars);

        if(method_exists($obj, $method)){
            return $obj->$method() ?? false;
        } else {
            // TODO mostra erro em dev
        }
            
    }

    private static function create(Service $service, $vars){
        $props = get_object_vars($service);
        $service->set($vars, array_keys($props));
    }   

    private function set($vars, $props){
        foreach ($vars as $k => $var) {
            if(in_array($k, $props))
                $this->$k = $var;
        }
    }

}