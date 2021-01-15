<?php 

class Module {

    private static $instance = false;
    private $modules = [];
    private $pattern;
    private $atual_module;

    private function __construct(){
        
        $config = Config::create();
 
        foreach ($config::get('modules') as $slug => $module) {
            if($module['type'] == 'pattern'){
                $module['slug'] = $slug;
                $this->pattern = $module;
            }
            else {
                $this->modules[$slug] = $module;
            }
                
        }
       
    }


    public static function create(){
        if(!self::$instance)
            self::$instance = new Module();
        return self::$instance;
    }

    public static function start($module_slug){
        
        if(in_array($module_slug, array_keys(self::$instance->modules))){
            $module = self::$instance->modules[$module_slug];
        } else {
            $module = self::$instance->pattern;
        }

        foreach ($module['includes'] as $value) {
            include "../".$module['dir']."/".$value;
        }

        self::$instance->atual_module = $module;
    }

    public static function config(){
        $class = self::$instance->atual_module['class_name'];
        $objTest = new $class();
        self::config_module($objTest, $class);
    }

    private static function config_module(ModuleInterface $objTest, string $module){
        $index = self::$instance->atual_module['type'] != "pattern" ? 1 : 0;
        $route = Route::getRouteOf(Request::get($index));
        $module::config($route);
    }

    public static function render(){
        $class = self::$instance->atual_module['class_name'];
        $class::render();
    }

}