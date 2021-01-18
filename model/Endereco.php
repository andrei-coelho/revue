<?php 

class Endereco extends Revue\src\Model {
    
    /**
     * @model-foreign: id_usuario
     */
    public $idUser;

    /**
     * @model-attr: local
     */
    public $rua;

    /**
     * @model-join: Telefone
     */
    public $telefones = [
        "order" => "id DESC",
        "limit" => 10
    ];

}