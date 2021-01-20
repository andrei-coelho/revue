<?php 

use Revue\src\Route as Route;
Route::ignore("api");

Route::req('/home\/{var_name}\/?{test}?/', 'main');