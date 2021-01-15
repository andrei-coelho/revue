<?php 

$vars = self::getData();

self::export('level2A', [
    'say' => "olá mundão!!!"
]);


self::data([
    "func" => $vars["callback"](),
    "num"  => $vars['number']
]);
