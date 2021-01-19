<?php 

use Revue\src\Service as Service;

$status = Service::call("EmailService", "exec", [
    "key" => "12345",
    "errado" => "nao existe"
]);

var_dump($status);

/*
$user = new User("User Test", [
    new Endereco("test 1", [
        new Telefone("telefone 1 A"),
        new Telefone("telefone 1 B")
    ]),
    new Endereco("test 2", [
        new Telefone("telefone 2 A"),
        new Telefone("telefone 2 B")
    ])
]);

$user->save();


$css = css();

$lista = [
    [
        "nome" => "Andrei",
        "idade" => 30
    ],
    [
        "nome" => "Gustavo",
        "idade" => 28
    ]
];


self::export("lista", $lista);

self::export("user", [
    "nome" => "Andrei"
]);

self::send("footer", [
    'number'   => 2,
    'callback' => function(){
        return 'chamou a função de callback';
    }
]);

self::data([
    "nome" => "Andrei",
    "img"  => url("img/test.jpg")
]);
*/