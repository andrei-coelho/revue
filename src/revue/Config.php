<?php 


class Config {

    private static $instance = false;
    private $data;

    private function __construct(){
        $this->data = Spyc::YAMLLoad('../conf.yaml');
        // print_r($this->data);
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

}