<?php 

namespace Revue\modules;

interface ModuleInterface {
    
    public static function config($route);
    public static function render();

}