<?php 

class Components implements ModuleInterface {

    private static $components = []; // componentes registrados
    private static $toRender = []; // componentes para renderizar
    private static $atualComp; // componente atual

    private static $js = []; // todos os javascripts
    private static $css = []; // todos os css
    
    private static $exports = []; // variaveis passadas entre componentes
    private static $tempData = []; // variaveis que serão usadas no html
    private static $ObjJsonList = []; // lista de ObjJson para ser gerado o json final

    public static function register(array $data){
        /** Example:
            [
               "tag" => [
                   "js" => ["file1", "file2", ...],
                   "css"=> ["file1", "file2", ...],
               ]
            ]
        */
        
        foreach ($data as $key => $value) {
            self::$components[$key] = $data[$key];
        }

    }

    private static function export($key, $vars){
        self::$ObjJsonList[$key] = new ObjJson($key, $vars);
    }


    private static function data(array $vars){
       
        self::$tempData = $vars;
    }


    private static function send(string $to, array $vars){

        // TODO: 
        // se estiver em modo de desenvolvimento
        // mostrar os erros  

        // verificar se o component foi registrado

        self::$exports[$to] = $vars;
    }

    private static function receive(){

        // TODO: 
        // se estiver em modo de desenvolvimento
        // mostrar os erros
        return isset(self::$exports[self::$atualComp]) 
               ? self::$exports[self::$atualComp]
               : false;
    }

    public static function config($tag){

        $comp = isset(self::$components[$tag]) ? 
                self::$components[$tag] : 
                false;
        
        if($comp) {

            $file = "../app/components/".$comp['file'].".html";

            if(file_exists($file)){

                if(isset($comp['css'])){
                    if(is_array($comp['css'])){
                        foreach($comp['css'] as $css){
                            if(!in_array($css, self::$css))
                                self::$css[] = $css;
                        }
                    }
                    else {
                        if(!in_array($comp['css'], self::$css)) 
                        self::$css[] = $comp['css'];
                    }
                }
                
                if(isset($comp['js'])){
                    if(is_array($comp['js'])){
                        foreach($comp['js'] as $js){
                            if(!in_array($js, self::$js))
                                self::$js[] = $js;
                        }
                    }
                    else {
                        if(!in_array($comp['js'], self::$js)) 
                        self::$js[] = $comp['js'];
                    }
                }

                $html = preg_match_all(
                    '/<component>(\w+)<\/component>/', 
                    file_get_contents($file), 
                    $out
                );

                foreach($out[0] as $k => $o){
                    $tagNameComp = $out[1][$k];
                    $comp['to_replace'][] = [
                        'tag_html' => $o,
                        'tag_comp' => $tagNameComp
                    ];
                }
                
                $comp['file'] = $file;
                self::$toRender[$tag] = $comp;

                if(isset($comp['to_replace']))
                    foreach($comp['to_replace'] as $toChange)
                        self::config($toChange['tag_comp']);
                

            }
            

        } 

    }


    public static function render(){
        $html = [];
        foreach (self::$toRender as $tag => $comp){
            self::$atualComp = $tag;
            $html[$tag] = self::generate_html($comp);
        }
        $html = self::merge($html);
    }


    private static function generate_html($comp){
        // insere o controller para gerar o html
        if(isset($comp['controller'])){
            $controller = "../app/controller/".$comp['controller'].".php";
            include $controller;
        }
        
        $html = file_get_contents($comp['file']);

        foreach(self::$tempData as $key => $val){
            $k = '{$'.$key.'}';
            $html = str_replace($k, $val, $html);
        }
        
        self::$tempData = [];

        return $html;

    }

    private static function merge($htmls){

        $changed = array_reverse($htmls);

        foreach($changed as $tag => &$html){

            $component = self::$toRender[$tag];
            

            if(isset($component['to_replace'])){

                foreach($component['to_replace'] as $to_replace){
                    
                    if(isset($changed[$to_replace['tag_comp']])){
                        $parent = $changed[$to_replace['tag_comp']];
                        $replace = $to_replace['tag_html'];
                        $changed[$tag] = str_replace($replace, $parent, $html);
                    }
                    
                }
            }
        }

        $scriptObjs  = "<script>";
        $scriptObjs .= "const URL = '".Config::url()."';";
        $scriptObjs .= "const Revue = {";
        foreach(self::$ObjJsonList as $obj){
            $scriptObjs .= $obj->render();
        }
        $scriptObjs = substr($scriptObjs, 0, -1)."}</script>";
        echo $scriptObjs;

        foreach(array_reverse($changed) as $ch){
            echo $ch;
            break;
        }

    }

    public static function js_files(){

        $files = [];
        foreach (self::$js as $js) {
            $files[] = Config::url()."js/".$js.".js";
        }

        return $files;
    }

    public static function css_files(){

        $files = [];
        foreach (self::$css as $css) {
            $files[] = Config::url()."css/".$css.".css";
        }
        
        return $files;
    }

}