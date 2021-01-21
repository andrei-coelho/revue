<?php 


/**
 * @block address
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

}