<?php 

echo js();

echo css();

self::export("footer", [
    'number'   => 2,
    'callback' => function(){
        return 'chamou a função de callback';
    }
]);

self::data([
    "nome" => "Andrei",
    "img"  => url("img/test.jpg")
]);