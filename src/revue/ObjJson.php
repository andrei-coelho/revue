<?php 


class ObjJson {

    private $key;
    private $data;

    public function __construct(string $key, array $data){
        $this->data = $data;
        $this->key  = $key;
    }

    public function render(){
        $json = API::render($this->data);
        $const = " ".$this->key." : ";
        return $const.$json.",";
    }

}