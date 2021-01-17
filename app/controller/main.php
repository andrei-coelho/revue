<?php 

use Revue\src\Model as Model;
use SQLi\SQLi as sqli;
/*
// com limite de 1
$res = sqli::query(
    "SELECT 
    usuario.id, usuario.nome,
    endereco.id as end_id, endereco.id_usuario, endereco.local 
    FROM 
        usuario 
    JOIN 
        endereco
    ON  
        endereco.id = (
            SELECT end1.id FROM endereco as end1 
            WHERE end1.id = usuario.id ORDER BY 
            end1.local DESC LIMIT 1
        )
    ORDER BY
        usuario.nome ASC, endereco.local DESC
");

foreach ($res->fetchAllAssoc() as $key => $value) {
   print_r($value); echo "<br>";
}
*/
Model::select("User");

/*

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