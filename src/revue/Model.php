<?php 

// $dados = Model::from('classname')
//          ->where('')
//          ->order('')
//          ->limit('')
//          ->get();

abstract class Model {

    public function json(){
        // aqui ele transforma em json o model filho
    }

    public static function sql($classname){
        // aqui ele gera o objeto de um model fazendo consulta sql
    }

}