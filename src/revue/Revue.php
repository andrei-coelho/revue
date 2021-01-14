<?php 

class Revue {

    public function __construct(){
        $this -> start();
    }

    private function start(){
        // aqui inicia o processo de criação de todas as
        // variáveis, objetos e configuração do app
        
        Request::open();
        
        include "../app/components.php";
        include "../app/routes.php";

        $this->config();
    }

    private function config(){
        // aqui ele chama um componente pricipal usando rotas
        $tag  = Route::getRouteOf(Request::get(0));
        Components::config($tag);
        $this->render();
    }

    private function render(){
        Components::render();
    }

    private function close(){
        // aqui ele fecha qualquer tipo de conexão aberta
    }



    private function web(){
        // aqui ele gera todos os cabeçalhos web
    }

    private function api(){
        // aqui ele gera todos os cabeçalhos api
    }


}