<?php 

spl_autoload_register(function($class) {

    if(($str = strstr($class, "\\"))) $class = $str; 

    $files = [
        "../src/revue/".$class.".php",
        "../model/".$class.".php",
    ];

    foreach ($files as $file) {
        if(file_exists($file)){
            include $file;
        }
    }

});