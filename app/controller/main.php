<?php 

self::export("footer", [
    'number'   => 2,
    'callback' => function(){
        return 'chamou a função de callback';
    }
]);



self::data([
    "nome" => "Andrei"
]);