<?php 


require "../packages/revue/Config.php";
require "../packages/revue/src/Spyc.php";

use Revue\Config as Config;

Config::create();

// model register
// service register
spl_autoload_register(function($class) {
    
    
    if(($str = strstr($class, "\\"))) $class = $str; 

    // Todo - Ler todos os diretórios do model

    $files = [
        "../model/".$class.".php",
        "../services/".$class.".php",
    ];

    foreach ($files as $file) {
        if(file_exists($file)){
            include $file;
        }
    }
    

});

// pack register
spl_autoload_register(function($class) {

    $parts     = explode("\\", $class);
    $realClass = array_pop($parts);
    $pack  = "";
    $realPack = "";

    foreach ($parts as $k => $p) {
        $el = strtolower($p);
        if($k == 0) $realPack = $el;
        $pack .= $el."/";
    }

    $file  = "../packages/".$pack.$realClass.".php";

    if(Config::is_pack($realPack) && file_exists($file) && !class_exists($class)){
        include $file;
    } else {
        echo "";
        // verifica se não está em produção e mostra o erro
    }

});




