<?php 
/**
 * Routes of Webpage
 */

use Revue\src\Route as Route;



Route::req('/(home|main)\/?{sub}?\/?{slug}?/', 'home');
// Route::mid('/(main)\/{sub}!\/?{slug}?/', 'midd');
Route::req('contato', 'contato');

