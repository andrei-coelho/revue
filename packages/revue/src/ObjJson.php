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

    private function json($obj){

        return $this->hot_json_encode($obj, 
            JSON_PRESERVE_ZERO_FRACTION | 
            JSON_PARTIAL_OUTPUT_ON_ERROR |
            JSON_UNESCAPED_UNICODE
        );
    }

    private function hot_json_encode ($var, int $options = 0, int $depth = 512) {
        return json_encode($this->transform_value($var), $options, $depth);
    }
    
    private function hot_json_last_error(){
    
        switch (json_last_error()) {
    
            case JSON_ERROR_NONE:
                return [0,'No errors'];
            case JSON_ERROR_DEPTH:
                return [1,'Maximum stack depth exceeded'];
            case JSON_ERROR_STATE_MISMATCH:
                return [2,'Underflow or the modes mismatch'];
            case JSON_ERROR_CTRL_CHAR:
                return [3,'Unexpected control character found'];
            case JSON_ERROR_SYNTAX:
                return [4,'Syntax error, malformed JSON'];
            case JSON_ERROR_UTF8:
                return [5,'Malformed UTF-8 characters, possibly incorrectly encoded'];
            case JSON_ERROR_RECURSION:
                return [6,'Recursion detected'];
            case JSON_ERROR_INF_OR_NAN:
                return [7,'INF or NAN value cannot be JSON encoded'];
            case JSON_ERROR_UNSUPPORTED_TYPE:
                return [8,'Unsupported type'];
            default:
                return [9,'Unknown error'];
                
        }
    
    }
    
    private function object_instance(string $class, array $json){
    
        $reflex =  new ReflectionClass($class);
        $comment = $reflex -> getDocComment();
        $inspector = $this->inspec_comments($comment);
        $bloquers = [];
    
        if($inspector['bound'] === false && ($parent = $reflex -> getParentClass()) !== false)
            $bloquers = $this->get_inherited_block_values($parent, "DECODE");
    
        return $this->set_values($reflex, $json, $inspector, $bloquers);
        
    }
    
    private function get_inherited_block_values(\ReflectionClass $parent, string $type){
    
        $comment = $parent -> getDocComment();
        $inspector = $this->inspec_comments($comment);
        $bloquers = [];
    
        if($inspector['bound'] === false && ($parentP = $parent -> getParentClass()) !== false)
            $bloquers = $this->get_inherited_block_values($parentP, $type);
        
        foreach($inspector['block'] as $field => $ty)
            if($ty == $type || $ty == "ALL") $bloquers[] = $field;
            
        return $bloquers;
    }
    
    private function set_values(\ReflectionClass $reflex, array $json, array $inspector, array $bloquers){
    
        $obj = $reflex -> newInstanceWithoutConstructor();
        
        foreach ($json as $field => $value) {
            
            if($value === null || in_array($field, $bloquers))
                continue;
            
            if(array_key_exists($field, $inspector['block']))
                if($inspector['block'][$field] == "ALL" || $inspector['block'][$field] == "DECODE")
                    continue;
    
            if(array_key_exists($field, $inspector['kind']) && $inspector['kind'][$field] != ""){
                $type = $inspector['kind'][$field];
                switch($type){
                    case "string": 
                        if(is_array($value))
                            $value = self::array_to_string($value);
                        else
                            $value = (string)$value;
                    break;
                    $value = (string)$value; break;
                    case "int": $value = (int)$value; break;
                    case "float": $value = (float)$value; break;
                    case "bool": $value = (bool)$value; break;
                    case "array": $value = (array)$value; break;
                    default: $value = $this->object_instance($type, $value);
                }
            }
    
            if($reflex -> hasProperty($field)){
                $prop = $reflex -> getProperty($field);
                $prop -> setAccessible(true);
                $prop -> setValue($obj, $value);
            }
            
        }
    
        return $obj;
    }
    
    public static function array_to_string(array $arr){
        $str = "[";
        foreach($arr as $key => $val) {
            if(!is_numeric($key)) $str .= $key . ":";
            if(is_array($val))
                $str .= self::array_to_string($val).",";
            else
                $str .= $val.",";
        }
        return substr($str,0,-1) . "]";
    }
    
    private function inspec_comments($comment){
    
        $inspec = [
            "kind" => [],
            "block" => [],
            "bound" => false
        ];
        
        if($comment !== false) {
            
            preg_match_all('/(@(kind|block)+\s+[\w:]+|@bound)/', $comment, $array);
            
            foreach ($array[1] as $value) {
    
                preg_match('/(@([\w]+)\s+([\w:]+)|@(bound))/', $value, $params);
    
                if(isset($params[4]) && $params[4] == "bound"){
                    $inspec['bound'] = true;
                    continue;
                }
    
                switch($params[2]){
        
                    case "kind": 
                        if( strpos( $params[3],":" ) !== false) {
                            $values = explode(":", $params[3]);
                            $inspec['kind'][$values[0]] = $values[1]; 
                        }
                    break;
        
                    case "block": 
                        if( strpos( $params[3],":" ) !== false) {
                            $values = explode(":", $params[3]);
                            $inspec['block'][$values[0]] = strtoupper($values[1]); 
                        } else {
                            $inspec['block'][$params[3]] = "ALL";
                        }
                    break;
        
                }
        
            }
        }
    
        return $inspec;
    }
    
    private function transform_value ($val) {
    
        if (is_array($val))
            $newValue = $this->read_array($val);
        else
        if (is_object($val))
            $newValue = $this->read_object($val);
        else
            $newValue = $val;
    
        return $newValue;
    }
    
    private function read_object (Object $obj) {
        
        $nObj = get_class($obj); 
        $reflex =  new \ReflectionClass($obj);
        $inspector = $this->inspec_comments($reflex -> getDocComment());
        $newArray = [];
        $bloquers = [];
    
        if($inspector['bound'] === false && ($parent = $reflex -> getParentClass()) !== false)
            $bloquers = $this->get_inherited_block_values($parent, "ENCODE");
    
        $props = $reflex -> getProperties();
    
        foreach ($props as $prop){
            
            if($nObj != $prop->class && in_array($prop->name, $bloquers)) continue;
            if(array_key_exists($prop->name, $inspector['block']))
                if($inspector['block'][$prop->name] == "ALL" || $inspector['block'][$prop->name] == "ENCODE")
                    continue;
    
            $prop -> setAccessible(true);
            $newArray[$prop -> name] = $this->transform_value($prop -> getValue($obj));
    
        }
    
        return $newArray;
    }
    
    private function read_array (array $arr) {
    
        $newArray = [];
        foreach ($arr as $key => $val)
            $newArray[$key] = $this->transform_value($val);
    
        return $newArray;
    
    }

}