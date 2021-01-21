<?php 

/**
 * Components of Webpage
 */

Revue\modules\Components::register([

    "home" => [
        "controller" => "main",
        "file"       => "home",
        "js"         => ["file1", "file3"],
        "css"        => ["file2"],
    ],

]);
