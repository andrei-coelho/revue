<?php 

// $dados = Model::select('classname')
//          ->where('')
//          ->order('')
//          ->limit('')
//          ->get();

namespace Revue\src;

abstract class Model {

    // padrão de tabelas e atributos para 
    // nomes composto em camel case nas classes model
    // 'low' (lowercase) ou 'upp' (uppercase)
    // seguido por '_' , '-' ou '' (colado)
    public static $pattern = "low_";

    // public static $prefix = "";

    protected $id = 0;
    protected $clone; // para o update e segurança
    protected $fromConstructor = true;

    protected $dataInfo;
    protected $strSelect;

    public static function select($className){
        // verifica se a classe extende de Model
        // salva os dados após a leitura da diretiva
        // retorna uma instancia da classe
        $data = self::read_class($className);
        $sql  = "";

        foreach ($data as $key => $value) {
            print_r($value);echo "<br>";
            // echo "<br>";
        }
    }


    /// LENDO A CLASSE

    private static function read_class($className){

        $reflc = new \ReflectionClass($className);
        
        if(!$reflc->getParentClass()->name == 'Revue\src\Model'){
            return ['is_model' => false];
        }

        $data['table_name'] = preg_match(
                '/@model-table:\s*(\w+)/', 
                $reflc->getDocComment(), 
                $out
            ) ? $out[1] : strtolower($className);
        
        $data['attr'] = [];

        $valuesProp = $reflc->getDefaultProperties();
       
        foreach ($reflc->getProperties() as $k => $prop) {

            if($prop->class == $className){
                
                $prop->setAccessible(true);

                preg_match(
                    '/@model-(attr|join):\s*(\w+)/', 
                    $prop->getDocComment(), 
                    $out
                );

                $join = isset($out[1]) && $out[1] == "join" 
                      ? self::read_class($out[2])
                      : null;

                     
                $data['attr'][$prop->name] = [
                    "render"   => isset($out[1]) ? $out[1] : false,
                    "value"    => isset($out[1]) ? $out[2] : "",
                    "is_array" => is_array($valuesProp[$prop->name]),
                    "qtd"      => is_array($valuesProp[$prop->name]) ? $valuesProp[$prop->name][0] : 0,
                    "join"     => $join,
                    'is_model' => true
                ];
                
            }

        }
        
        return $data;

    }






    /// FUNÇÕES DO OBJETO FILHO

    public function json(){
        // retorna os dados em json do array de objetos do model filho
        echo "json";
    }

    public function get(){
        // retorna um array de objetos do model filho
    }

    public function where(array $args){

        $this->fromConstructor = false;

        $example1 = [
            //WHERE

            [// group 1
                
                "slug", "=", "slug1",
                // AND
                "total", ">", 200.50 
            ],
           // OR
            [ // group 2
                
                "slug", "=", "slug2",
                // AND
                "total", "<", 340 
            ]

        ];
        // saída: 
        // ... WHERE (slug = 'slug1' AND total > 200.50) OR (slug = 'slug2' AND total < 340)

        // seta o as condições do select
        // retorna os dados em array o model filho
    }

    public function order(string $by){

        $example = "something DESC";
        // saída
        // ORDER BY something DESC

        // orderna o resultado por um attr 
        
    }

    public function limit(int $limit){
        // seta a quantidade máxima
    }

    public function update(){
        // altera o objeto no banco
    }

    public function save(){
        // salva o objeto criado no banco de dados
        // esta função só salva objetos criado pelo construtor
        if($this->fromConstructor){
            // salva os dados
        }
    }

    public function delete(){
        // deleta um registro no banco de dados
    }

}