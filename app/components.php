<?php 

/**
 * Components of Webpage
 */

Revue\modules\Components::register([

    "home" => [
        "controller" => "main",
        "file"       => "home",
        "js"         => ["file1"],
        "css"        => ["file2"],
    ],

    "footer" => [
        "controller" => "footer",
        "file"       => "footer",
        "js"         => ["file1"],
        "css"        => ["file2"],
    ],

    "header" => [
        "file"       => "header",
    ],

    "menu" => [
        "file"       => "menu",
    ],

    "level1A" => [
        "file"       => "level1A",
    ],

    "level2A" => [
        "controller" => "level",
        "file"       => "level2A",
    ],

    "contato" => [
        "controller" => "contato",
        "file"       => "others/contato",
    ],

]);
