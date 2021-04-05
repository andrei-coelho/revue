<?php 

// TODO -> sessions e cookies

namespace Revue\src;

class Request {

    private $gets = [], $posts = [], $inputs = [];
    private static $instance = false;

    private function __clone() {}

    private function __construct(){

        if(($inputs = json_decode(file_get_contents('php://input'), true)) != null)
        $this->inputs     = $inputs;
        $this->posts      = $_POST;
        $this->gets       = isset($_GET['req']) ? $this->create($_GET['req']) : [];

    }

    public static function open(){

        if(!self::$instance){
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getCount(){

        return count($this->gets);
    }

    public static function saveSlugGet(array $gets){
        
        foreach ($gets as $var) {            
            self::$instance->gets[substr($var['slug'], 1, strlen($var['slug']) - 2)] = $var['value'];
        }

    }
   
    public static function get($key = -1){

        if($key > -1)
            return isset(self::$instance->gets[$key]) ? 
            self::$instance->gets[$key] : false;

        return self::$instance->gets;

    }

    public static function post($key = ""){

        if($key != "")
            return isset(self::$instance->posts[$key]) ? 
            self::$instance->posts[$key] : false;

        return count(self::$instance->posts) > 0 ? self::$instance->posts : false;

    }

    public static function input($key = ""){

        if($key != "")
            return isset(self::$instance->posts[$key]) ? 
            self::$instance->posts[$key] : false;
            
        return count(self::$instance->posts) > 0 ? self::$instance->posts : false;

    }

    private function create(string $url):array{

        $partes = explode("/", $url);
        $arr    = [];
        foreach ($partes as $value) if(trim($value) != "") $arr[] = $value;

        return $arr;

    }

}