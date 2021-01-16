<?php 

namespace Revue;

class Config {

    private static $instance = false;
    private $data, $url;

    private function __construct(){

        $this->data = src\Spyc::YAMLLoad('../conf.yaml');

        $this->url  = $this->data['production'] 
                    ? $this->data['production_url']
                    : $this->data['development_url'];

    }

    public static function create(){

        if(!self::$instance)
            self::$instance = new Config();

        return self::$instance;
    }

    public static function get(string $key = ""){
        
        $conf = self::$instance->data;

        if($key != "") 
            return (isset($conf[$key]) 
                        ? $conf[$key] 
                        : false);
                        
        return $conf;
    }

    public static function url(){

        return self::$instance->url;
    }

    public static function is_in_production(){

        return self::$instance->data['production'];
    }

    public static function is_pack($pack){

        return in_array($pack, self::$instance->data['pack']);
    }

}