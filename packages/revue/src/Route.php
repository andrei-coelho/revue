<?php 

namespace Revue\src;

class Route {

    private static $routes = [];
    private static $middle = [];

    private static $request = false;
    private static $target = false;
    private static $midware = false;

    public static function req(string $route, string $target){  
        
        if(self::$target) return;

        if(!self::$request){
            self::$request = implode("/", Request::get());
        }

        if(self::$request == "" && self::is_main($route)){
            self::$target = $target;
            return;
        }

        if(self::is_regex($route)){

            $data = self::tranform_data($route);
            $vars = [];
            
            foreach ($data as $d) {
                $vars[] = $d['slug'];
                $route = str_replace($d['to_replace'], $d['value'], $route);
            }

            if(self::is_target($route, self::$request)){
                
                Request::saveSlugGets($vars);
                self::$target = $target;
                return;

            } 
            
            return;
        }

        if (strpos(self::$request, $route) !== false){
            self::$target = $target;
        }

        

    }

    public static function mid($route, $target){

        if(self::$midware) return;

        if(!self::$request){
            self::$request = implode("/", Request::get());
        }

        if(self::$request == "" && self::is_main($route)){
            self::$midware = $target;
            return;
        }

        if(self::is_regex($route)){
            
            
            $data = self::tranform_data($route);
            $vars = [];
            
            foreach ($data as $d) {
                $vars[] = $d['slug'];
                $route = str_replace($d['to_replace'], $d['value'], $route);
            }

            if(self::is_target($route, self::$request)){
                
                self::$midware = $target;
                return;

            } 
            
            return;
        }
        

        if (strpos(self::$request, $route) !== false){
            self::$midware = $target;
        }

    }

    public static function getMiddleware(){

        return self::$midware;
    }

    public static function getTarget(){

        return self::$target;
    }

    private static function tranform_data($reg){

        preg_match_all('/{(\w+)}(!|\?)/', $reg, $out);
        // create arrays
        $data     = [];

        foreach ($out[1] as $key => $var) {
            $data[] = [
                "slug" => $var,
                "to_replace" => $out[0][$key],
                "value" => $out[2][$key] == "!" ? "[^\/]+" : "[^\/]*",
            ];
        }

        return $data;

    }

    private static function is_target($test, $target){
        return preg_match($test, $target);
    }

    private static function is_regex($test){
        return preg_match('/\/[^#]+\//', $test);
    }

    private static function is_main($test){
        return preg_match('/main/', $test);
    }

}