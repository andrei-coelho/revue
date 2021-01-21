<?php 

/**
 * Padrão do JSON
 * 
 * {
 *  "error":false,
 *  "message":200,
 *  "data":{} 
 * }
 * 
 */

namespace Revue\modules;


class API implements ModuleInterface {

    private static $response = false;

    public static function config($route){
        
        if(!$route) {
            self::error(404);
            return;
        }

        include "../api/controller/".$route.".php";

    }

    public static function render(){

        if(self::$response){
            echo \Revue\src\ObjJson::hot_json_encode(self::$response, 
                JSON_PRETTY_PRINT | 
                JSON_PRESERVE_ZERO_FRACTION | 
                JSON_PARTIAL_OUTPUT_ON_ERROR |
                JSON_UNESCAPED_UNICODE
            );
        }
        
    }

    private static function response($data){

        self::$response = [
            "error" => false,
            "code"  => 200,
            "data"  => $data
        ];
    }

    private static function error($number){
        
        self::$response = [
            "error" => true,
            "code"  => $number,
            "data"  => null
        ];
    }

}