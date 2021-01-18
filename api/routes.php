<?php 

use Revue\src\Route as Route;
Route::ignore("api");

Route::req('/(main)/', 'main');