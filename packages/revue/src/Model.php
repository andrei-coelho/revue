<?php 

namespace Revue\src;

use SQLi\SQLi as sqli;

/**
 * Bloqueia a exibição de variáveis em JSON
 * 
 * @block pattern
 * @block clone
 * @block fromConstructor
 * @block dataInfo
 * @block query
 * @block className
 * @block is_model
 * @block attrs
 * @block joins
 * 
 */

abstract class Model {

    // padrão de tabelas e atributos para 
    // nomes composto em camel case nas classes model
    // 'low' (lowercase) ou 'upp' (uppercase)
    // seguido por '_' , '-' ou '' (colado)
    public static $pattern = "low_";

    // public static $prefix = "";

    public $id = 0;

    protected $clone; // para o update e segurança
    protected $fromConstructor = true;

    protected $dataInfo;
    protected $query;
    protected $className;
    protected $is_model = false;

    protected $attrs = [];
    protected $joins = [];



    public function get(){
        // retorna um array de objetos do model filho
        // $table = $join['join']->dataInfo['table_name'];
        $res  = sqli::query($this->query);
        $list = [];

        if($res){
            $data = $res->fetchAllAssoc();
            $list = $this->generate_objs($data);
        } else {
            /// TODO - mostra erro
            
        }
        
        return $list;
    }

    public function order(string $by, $join = ""){

        $this->query .= "ORDER BY ".$by." ";
        return $this;

    }

    public function limit(string $limit){
        $this->query .= "LIMIT ".$limit;
        return $this;
    }


    public function save(){
        // salva o objeto criado no banco de dados
        // esta função só salva objetos criado pelo construtor
        if($this->fromConstructor){
            // salva os dados
            $this->insert([$this]);
            
        }
    }


    private function insert(array $arr_obj, $id_father = null, $table_name = null){
        
        // nomes de atributos que não podem ser usados
        $nouse     = ["id", "pattern", "clone", "fromConstructor", "dataInfo", "query", "className", "is_model", "attrs", "joins"];
        
        $qlast     = []; // querys insert que serão criadas depois
        $qinsert   = "INSERT INTO "; // query insert
        $table     = false;
        $attrbs    = false;
        $attsStr   = " ( id, ";
        $fatherReg = false;


        if($id_father){
            $fatherReg = "id_".$table_name;
            $attsStr  .= $fatherReg.", ";
            $nouse[]   = $fatherReg;
        }

        $usesAtts = [];

        foreach ($arr_obj as $obj) {

            $valuesStr = "( null, ".($id_father? $id_father.", " : "");

            $props = get_object_vars($obj);
            $reflc = new \ReflectionClass(get_class($obj));
        
            if(!$table) {

                $table = preg_match(
                    '/@model-table:\s*(\w+)/', 
                    $reflc->getDocComment(), 
                    $out
                ) ? $out[1] : strtolower($reflc->name);

                $qinsert .= $table;
            }
            
            foreach ($props as $var => $value) {

                $prop = $reflc->getProperty($var);
                $prop -> setAccessible (true);

                preg_match(
                    '/@model-(attr|join):\s*(\w+)/', 
                    $prop->getDocComment(), 
                    $out
                );

                if(!$attrbs && !in_array($var, $nouse)){
                    // insere os atributos na query
                    if($out && $out[1] == "attr"){
                        $usesAtts[] = $out[2];
                        $attsStr .= $out[2].", ";
                    }
                    
                }

                if($out && $out[1] == "join"){
                    $qlast[] = $value;
                    continue;
                }

                if(isset($out[1]) && in_array($out[2],$usesAtts)){
                    if(is_string($value)){
                        $value = "\"".addslashes($value)."\"";
                    } 
                    
                    if(!is_array($value)){
                        $value = $valuesStr .=  $value.", ";
                    }
                }
                
            }

            if(!$attrbs){
                $attsStr = substr($attsStr, 0, -2);
                $qinsert .= $attsStr." ) VALUES ";
                $attrbs = true;
            }

            $qinsert .= substr($valuesStr, 0, -2)." ), ";

        }

        $qinsert = substr($qinsert, 0, -2).";";
        $newid = sqli::exec($qinsert, true);

        if($newid){
            if($qlast){
                foreach ($qlast as $q) {
                    $this->insert($q, $newid, $table);
                }
            }
        } else {
            // TODO - Mostra erro em dev
        }   


    }


    public static function select($className){
        // verifica se a classe extende de Model
        // salva os dados após a leitura da diretiva
        // retorna uma instancia da classe
        $obj = self::read_class($className);
        if(!$obj->is_model){
            // TODO: mostra erro em desenvolvimento
            return null;
        }
        
        return $obj;
    }



    /// FUNÇÕES AUXILIARES

    private static function read_class($className){

        $reflc = new \ReflectionClass($className);
        $obj = $reflc->newInstanceWithoutConstructor();
        $obj ->fromConstructor = false;
        $obj ->className = $className;

        if(!$reflc->getParentClass()->name == 'Revue\src\Model'){
            return $obj;
        }

        $data['table_name'] = preg_match(
                '/@model-table:\s*(\w+)/', 
                $reflc->getDocComment(), 
                $out
            ) ? $out[1] : strtolower($className);
        
        $data['attr'] = [];

        $valuesProp = $reflc->getDefaultProperties();
       
        foreach ($reflc->getProperties() as $k => $prop) {

            if($prop->name == "id" || $prop->class == $className){
                
                $prop->setAccessible(true);
                
                if($prop->name != "id"){

                    preg_match(
                        '/@model-(attr|join|foreign):\s*(\w+)/', 
                        $prop->getDocComment(), 
                        $out
                    );
    
                    $join = isset($out[1]) && $out[1] == "join" 
                          ? self::read_class($out[2])
                          : false;
    
                } else {
                    $out    = [];
                    $out[1] = "attr";
                    $out[2] = "id";
                }
                
                $isarr = is_array($valuesProp[$prop->name]); 
                     
                $data['attr'][$prop->name] = [
                    "render"   => isset($out[1]) ? $out[1] : false,
                    "name"     => isset($out[1]) ? $out[2] : "",
                    "prop"     => $prop->name,
                    "is_array" => $isarr,
                    "limit"    => $isarr && isset($valuesProp[$prop->name]['limit']) ? $valuesProp[$prop->name]['limit'] : 0,
                    "order"    => $isarr && isset($valuesProp[$prop->name]['order']) ? $valuesProp[$prop->name]['order'] : 0,
                    "join"     => $join
                ];
                
                $obj->dataInfo = $data;
                $obj->is_model = true;

            }

        }

        $table = $obj->dataInfo['table_name'];
        
        foreach ($obj->dataInfo['attr'] as $att) {
            
            if(!$att['render']) continue;
            
            if($att['render'] == "join") {
                $obj->joins[$att['name']] = $att;
                continue;
            }

            $obj->attrs[$att['name']] = $att;

        }

        $sql = "SELECT ";
        $atts = $table.".".implode(", ".$table.".", array_column($obj->attrs, 'name'));
        $sql .= $atts." FROM ".$table." ";

        $obj->query = $sql;
        
        return $obj;

    }


    private function generate_objs(array $datas){
        
        $idsPrimary = array_column($datas, "id");
        $idTotal = count($idsPrimary);
        
        $where = [];
        $tableName = "id_".$this->dataInfo['table_name'];
        
        foreach ($idsPrimary as $id) {
            $where[] = $tableName;
            $where[] = "=";
            $where[] = $id;
            $where[] = "OR";
        }

        array_pop($where);

        $listJoinObj = [];
        
        foreach ($this->joins as $className => $join) {
            
            $objJoin = $join['join'];
            $class   = $join['name'];
            $limit   = $join['limit'] * $idTotal;
            $order   = $join['order'];
            

            $listJoinObj[$className] = 
            $objJoin
            ->where($where)
            ->order($order)
            ->limit($limit)
            ->get();

        }

        $foreign = false;

        foreach($this->joins as $key => $join){
            foreach ($join['join']->dataInfo['attr'] as $val) {
                
                if($val['render'] == "foreign"){
                    $foreign[$key] = [ 
                        "prop"     => $join['prop'],
                        "key_sec"  => $val['prop']
                    ] ;
                }
            }
        }
    
        

        $list = [];

        foreach ($datas as $data) {

            $reflc        = new \ReflectionClass($this->className);
            $objPrincipal = $reflc->newInstanceWithoutConstructor();

            foreach ($data as $index => $val) {
                $prop = $reflc->getProperty($this->attrs[$index]['prop']);
                $prop -> setAccessible (true);
                $prop -> setValue($objPrincipal, $val);
            }
            
            if($foreign){

                $classesForeign = array_keys($foreign);

                foreach ($classesForeign as $class) {
                    
                    $i = 0;
                    $objoins = [];

                    while (isset($listJoinObj[$class][$i])) {

                        $at = $foreign[$class]['key_sec'];
                        if($listJoinObj[$class][$i]->$at == $objPrincipal->id){
                            
                            $objoins[] = $listJoinObj[$class][$i];
                        }
                        $i++;
                    }


                    $prop = $reflc->getProperty($foreign[$class]['prop']);
                    $prop -> setAccessible (true);
                    $prop -> setValue($objPrincipal, $objoins);
           
                }
            }

            $list[] = $objPrincipal;
            
        }
        
    

        return $list;
    }

    public function where(array $args){

        $str = $this->transform_op_where_in_string($args);
        $this->query .= "WHERE ".$str;
    
        return $this;

    }

    private function transform_op_where_in_string($args){
        
        $operators = ["=", "<", ">", "<>", ">=", "<=", "BETWEEN", "NOT", "OR", "AND"];

        $str = "";

        $attrbs = array_column($this->attrs, 'name');

        foreach ($args as $arg) {

            if(is_array($arg)){
                $str .= "( ";
                $str .= $this->transform_op_where_in_string($arg).") ";
                continue;
            } 
            
            if(is_string($arg) && !in_array($arg, $operators) && !in_array($arg, $attrbs)){
                $str .= "\"".addslashes($arg)."\" ";
                continue;
            }

            $str .= !in_array($arg, $operators) && in_array($arg, $attrbs)
                    ? $this->dataInfo['table_name'].".".$arg." "
                    : $arg." ";
            
        }

        return $str;
    }

    

}