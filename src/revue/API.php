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


class API implements ModuleInterface {

    private static $response = false;

    public static function config($route){
        
        if(!$route) {
            self::error(404);
            return;
        }

        include "../api/controller/".$route.".php";

    }

    public static function render($json = false){

        if(!$json && self::$response){
            echo json_encode(self::$response, 
                JSON_PRETTY_PRINT | 
                JSON_PRESERVE_ZERO_FRACTION | 
                JSON_PARTIAL_OUTPUT_ON_ERROR |
                JSON_UNESCAPED_UNICODE
            );
        }
        
        if($json){
            return json_encode($json, 
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