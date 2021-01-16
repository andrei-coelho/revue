<?php 

namespace Revue\src;

class Module {

    private static $instance = false;
    private $modules = [];
    private $pattern;
    private $atual_module;

    private function __construct(){
        
        foreach (\Revue\Config::get('modules') as $slug => $module) {
            
            $module['slug'] = $slug;

            if($module['type'] == 'pattern'){
                $this->pattern = $module;
            }
            else {
                $this->modules[$slug] = $module;
            }
                
        }
       
    }


    public static function create(){
        
        if(!self::$instance)
            self::$instance = new self();
        return self::$instance;
    }

    public static function start($module_slug){
        
        if(in_array($module_slug, array_keys(self::$instance->modules))){
            $module = self::$instance->modules[$module_slug];
        } else {
            $module = self::$instance->pattern;
        }

        include "../".$module['dir']."/routes.php";

        self::$instance->atual_module = $module;
    }

    public static function getSlug(){
        if(self::$instance->atual_module)
            return self::$instance->atual_module['slug'];
    }

    public static function config(){

        $module = self::$instance->atual_module;

        foreach ($module['includes'] as $value) {
            include "../".$module['dir']."/".$value;
        }

        $class = "Revue\\modules\\".$module['class_name'];
        $objTest = new $class();
        self::config_module($objTest, $class);
    }

    private static function config_module(\Revue\modules\ModuleInterface $objTest, string $module){
        $index = self::$instance->atual_module['type'] != "pattern" ? 1 : 0;
        $target = Route::getTarget();
        $module::config($target);
    }

    public static function render(){
        $class = "Revue\\modules\\".self::$instance->atual_module['class_name'];
        $class::render();
    }

}