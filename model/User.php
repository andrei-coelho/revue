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
    public $address = [1];


    function __construct($nomeUser){
        $this->nomeUser = $nomeUer;
    }

}