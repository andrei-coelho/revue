<?php 
use Revue\src\Model as Model;

$usuarios = Model::select("User")
            ->order("id DESC")
            ->get();


            
self::response($usuarios);