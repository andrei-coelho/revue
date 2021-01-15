<?php 

/**
 * Components of Webpage
 */

Components::register([

    "home" => [
        "controller" => "main",
        "file"       => "home",
        "js"         => ["file1", "file2"],
        "css"        => ["file1", "file2"],
    ],

    "footer" => [
        "controller" => "footer",
        "file"       => "footer",
        "js"         => ["file1", "file2", "file3"],
        "css"        => ["file1", "file2", "file3"],
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
