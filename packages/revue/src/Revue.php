<?php 

namespace Revue\src;

class Revue {

    public function __construct(){
        $this -> start();
    }

    private function start(){
        
        // aqui inicia o processo de criação de todas as
        // variáveis, objetos e configuração do app
        Request::open();
        Module::create();
        Module::start(Request::get(0));
        $this->before_config();

    }

    private function before_config(){
        
        $status = Middleware::start(Route::getMiddleware());
        
        switch ($status) {
            case 'continue':
                $this->config();
                break;
            case 'redirect':
                header("Location: ".\Revue\Config::url().Middleware::getRedir());
                break;
            default:
                $this->config();
                break;
        }

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