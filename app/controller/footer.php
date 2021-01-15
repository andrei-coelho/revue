<?php 

$vars = self::receive();

self::send('level2A', [
    'say' => "olá mundão!!!"
]);


self::data([
    "func" => $vars["callback"](),
    "num"  => $vars['number'],
    "js"   => js()
]);
