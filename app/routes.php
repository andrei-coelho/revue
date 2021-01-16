<?php 
/**
 * Routes of Webpage
 */

use Revue\src\Route as Route;



Route::req('/(home|main)\/{sub}!\/?{slug}?/', 'home');
Route::mid('/(home|main)\/{sub}!\/?{slug}?/', 'midd');
Route::req('contato', 'contato');

