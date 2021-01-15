<?php 

class Route {

    private static $routes = [];

    public static function req($route, $target){

        if(is_array($route)){
            foreach ($route as $r) {
                self::$routes[$r] = $target;
            }
        } else {
            self::$routes[$route] = $target;
        }

    }

    public static function getRouteOf($route){
        if(!$route) $route = 'main';
        return isset(self::$routes[$route]) ? self::$routes[$route] : false;
    }

}