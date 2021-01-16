<?php 

namespace Revue\src;

class Middleware {

    private static $status = "continue";
    private static $redirect = "";

    public static function start($midd){
    

        if($midd){
            
            $file = "../".Module::getSlug()."/middleware/".$midd.".php";
            if(file_exists($file)){
                
                include $file; 
            } else {
                // todo - mostrar erros em desenvolvimento
                
            }
                
        }

        return self::$status;
    }

    private static function redirect($local){
        self::$status = "redirect";
        self::$redirect = $local;
    }

    public static function getRedir(){
        return self::$redirect;
    }

}