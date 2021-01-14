<?php 

class Route {

    private static $routes = [];

    public static function page($route, $page){

        if(is_array($route)){
            foreach ($route as $r) {
                self::$routes[$r] = $page;
            }
        } else {
            self::$routes[$route] = $page;
        }

    }

    public static function getRouteOf($route){
        if(!$route) $route = 'main';
        return isset(self::$routes[$route]) ? self::$routes[$route] : "error404";
    }

}