<?php 

class Revue {

    private $module, $route;

    public function __construct(){
        $this -> start();
    }

    private function start(){
        
        // aqui inicia o processo de criação de todas as
        // variáveis, objetos e configuração do app
        Request::open();
        Module::create();
        Module::start(Request::get(0));
        $this->config();

    }

    private function config(){

        Module::config();
        $this->render();

    }

    private function render(){

        
        Module::render();
        $this->close();
    
    }

    private function close(){
        // aqui ele fecha tudo e qualquer tipo de conexão aberta
        // encerra a aplicação
    }


}