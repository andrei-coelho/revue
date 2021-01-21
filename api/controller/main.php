<?php 

use Revue\src\Model as Model;
use Revue\src\Request as Request;

$var_name = Request::get('var_name');
$test = Request::get('test');

echo $test;

$usuarios = Model::select("User")
            ->order("id DESC")
            ->get();

 self::response($usuarios);


