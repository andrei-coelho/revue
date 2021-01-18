<?php 

/**
 * @model-table: telefones  
 */

class Telefone extends Revue\src\Model {

    /**
     * @model-attr: numero
     */
    public $numero;

    /**
     * @model-foreign: id_endereco
     */
    public $id_endereco;

}