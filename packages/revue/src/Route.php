<?php 

namespace Revue\src;

class Route {

    private static $routes = [];
    private static $middle = [];

    private static $request = false;
    private static $target = false;
    private static $midware = false;
    private static $ignore = "";

    public static function req(string $route, string $target){  
        
        if(self::$target) return;

        if(!self::$request){
            self::$request = implode("/", Request::get());
        }

        self::$request = str_replace(self::$ignore, "", self::$request);
        
        if(self::$request == "" && self::is_main($route)){
            self::$target = $target;
            return;
        }

        if(self::is_regex($route)){
            
            $data = self::tranform_data($route);
            $vars = [];
            
            foreach ($data as $d) {
                $vars[]['slug'] = $d['slug'];
                $route = str_replace($d['to_replace'], $d['value'], $route);
            }

            if(preg_match($route, self::$request, $out)){
                
                foreach ($vars as $k => $v) {
                    if(isset($out[$k + 1])){
                        $vars[$k]['value'] = $out[$k + 1];
                    }
                }

                Request::saveSlugGet($vars);
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
                $vars[]['slug'] = $d['slug'];
                $route = str_replace($d['to_replace'], $d['value'], $route);
            }
            

            if(preg_match($route, self::$request, $out)){
                
                foreach ($vars as $k => $v) {
                    if(isset($out[$k + 1])){
                        $vars[$k]['value'] = $out[$k + 1];
                    }
                }

                Request::saveSlugGet($vars);
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

    public static function ignore($ignore){
        self::$ignore = $ignore;
    }

    private static function tranform_data($reg){

        preg_match_all('/({(\w+)})(\?)?/', $reg, $out);
        
        // create arrays
        $data     = [];

        foreach ($out[1] as $key => $var) {
            $data[] = [
                "slug" => $var,
                "to_replace" => $out[1][$key],
                "value" => "([^\/]+)",
            ];
        }

        return $data;

    }

    private static function is_regex($test){
        return preg_match('/\/[^#]+\//', $test);
    }

    private static function is_main($test){
        return preg_match('/main/', $test);
    }

}