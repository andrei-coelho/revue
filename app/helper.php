<?php 

function js(){
    $js = "";
    foreach (Revue\modules\Components::js_files() as $file) {
        $js .= '<script src="'.$file.'"></script>';
    }
    return $js;
}

function css(){
    $css = "";
    foreach (Revue\modules\Components::css_files() as $file) {
        $css .= '<link rel="stylesheet" href="'.$file.'">';
    }
    return $css;
}

function url(string $url = ""){
    return Revue\Config::url().$url;
}

function error($num = 404){
    Revue\modules\Components::execComponent('error'.$num);
}

function dateToBr($data){
    return date('d/m/Y', strtotime($data));
}