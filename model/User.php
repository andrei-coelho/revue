<?php 


/**
 * @model-table: usuario
 */
class User extends Revue\src\Model {

    /**
     * @model-attr: nome
     */
    public $nomeUser;

    /**
     * @model-join: Endereco
     */
    public $address = [
        "limit" => 2,
        "order" => "id"
    ];

    function __construct(string $nomeUser, array $address = []){
        $this->address = $address;
        $this->nomeUser = $nomeUser;
    }

}