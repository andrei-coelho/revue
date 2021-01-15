<?php 

function js(){
    $js = "";
    foreach (Components::js_files() as $file) {
        $js .= '<script src="'.$file.'"></script>';
    }
    return $js;
}

function css(){
    $css = "";
    foreach (Components::css_files() as $file) {
        $css .= '<link rel="stylesheet" href="'.$file.'">';
    }
    return $css;
}

function url(string $url = ""){
    return Config::url().$url;
}