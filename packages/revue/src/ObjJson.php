<?php 

namespace Revue\src;

class ObjJson {

    private $key;
    private $data;

    public function __construct(string $key, array $data){
        $this->data = $data;
        $this->key  = $key;
    }

    public function render(){

        $json = $this->json($this->data);
        $const = " ".$this->key." : ";

        return $const.$json.",";
    }

    private function json($json){

        return json_encode($json, 
            JSON_PRESERVE_ZERO_FRACTION | 
            JSON_PARTIAL_OUTPUT_ON_ERROR |
            JSON_UNESCAPED_UNICODE
        );
    }

}