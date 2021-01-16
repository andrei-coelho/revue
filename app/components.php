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

    "footer" => [
        "controller" => "footer",
        "file"       => "footer",
        "js"         => ["file1"],
        "css"        => ["file2"],
    ],

    "header" => [
        "file"       => "header",
        "js"         => ["header"],
    ],

    "menu" => [
        "file"       => "menu",
        "js"         => ["menu"],
    ],

    "level1A" => [
        "file"       => "level1A",
        "js"         => ["level1"],
    ],

    "level2A" => [
        "controller" => "level",
        "file"       => "level2A",
        "js"         => ["level2"],
    ],

    "contato" => [
        "controller" => "contato",
        "file"       => "others/contato",
        "js"         => ["contato"],
    ],

]);
