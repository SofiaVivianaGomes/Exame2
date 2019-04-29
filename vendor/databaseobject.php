<?php

namespace Vendor;
use PDO;

class DatabaseObject {

    protected static $database;
    protected static $table_name ="";
    protected static $db_columns =[];
    public $errors = [];

    /* INICIO DO CÓDIGO DO PADRÃO DE DESENHO: ACTIVE RECORD */

    
    static function set_database($database){
        self::$database=$database; // single reference for db
    }

    public static function find_by_sql($sql, $prepared=true){
        if (!$prepared) {
            $sql = self::$database->prepare($sql . ";");
        }
        //$result= self::$database->query($sql); 
        try{
            $sql->execute();
            //$sql->debugDumpParams();
            $errorInfo = $sql->errorInfo();
            //if(!$result){
            if(!$errorInfo){
                exit("Database query failed.");
            }
            //converter os resultados em objectos
            $object_array=[];
            while($record=$sql->fetch()){                
                $object_array[]=static::instatiate($record);
            }

            //com PDO não dá para fazer free result uma vez que
            //não há nenhuma referencia, tem de se fazer unset
            //unset($result);

            return $object_array;
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'.$sql->debugDumpParams());  
        }
    }

    public static function count_all(){ 
        $query = self::$database->prepare("SELECT count(*) FROM ". static::$table_name . ";");
        try{
            $query->execute(); 
            //$query->debugDumpParams();exit;
            //como só quero é o resultado do count que vem numa coluna e uma linha
            //posso usar o fetchColumn(fetchColumn - Retorna uma única coluna da próxima linha de um conjunto de resultados)
            $row = $query->fetchColumn();
            return $row;
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'. $query->debugDumpParams());  
        }
    }

    public static function find_all(){ 
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name.";"); //static para buscar o nome da class na altura
        return static::find_by_sql($query);
    }

    public static function find_by_id($id){
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE id = :id");
        $query->bindParam(':id',$id,PDO::PARAM_INT);
        
        $object_array= static::find_by_sql($query);

        if(!empty($object_array)){
            //retorna o primeiro elemento do inicio do array
            return array_shift($object_array);
        }else{
            return false;
        }
    }

    public static function find_all_paginated($limit,$offset){
        
        $query="SELECT * FROM ".static::$table_name ." ";
        $query.="Limit :limit ";
        $query.= "OFFSET :offset";
       //  echo $query;
        $sql = self::$database->prepare($query);
        $sql->bindParam(':limit',$limit,PDO::PARAM_INT);
        $sql->bindParam(':offset',$offset,PDO::PARAM_INT);
        
        return static::find_by_sql($sql);
    }

    protected static function instatiate($record){      
        $object = new static; //instantiate the current class with static (not the DatabaseObject)
        foreach($record as $property=>$value){
            if(property_exists($object,$property)){
                $object->$property = $value;
            }
        }
        return $object;
    }

    protected function validate(){
        $this->errors=[];

        // Add custom validations
        
        return $this->errors;
    }

    protected function create(){
        $this->validate();
        if(!empty($this->errors)){return false;}
        $attributes=$this->attributes();
        //var_dump($attributes); exit();
        $query = "INSERT INTO ".static::$table_name."(";
        $query.=join(',',array_keys($attributes));
        $query.=") VALUES ( :";
        $query.=join(',:',array_keys($attributes));
        $query.=")";
        $sql = self::$database->prepare($query); 
        $this->bindAttributes($sql);

        try{
            $sql->execute(); //$sql->debugDumpParams();exit;
            $errorInfo = $sql->errorInfo();
            if(!$errorInfo){
                //$this->safeFile(); // DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDESCOMENTAR
                return false;
            }else{
                $this->id=self::$database->lastInsertId();
                return true;
            }
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'.$sql->debugDumpParams());  
        }
    }

    protected function update(){
        $this->validate();
        if(!empty($this->errors)){return false;}
        $attributes_pairs = [];
        foreach(static::$db_columns as $column){
            $attributes_pairs[]="{$column} =:{$column}";
        }
        $query="UPDATE ".static::$table_name." SET ";
        $query.=join(',',$attributes_pairs);
        $query.=" WHERE `id` = :id LIMIT 1;";
        //var_dump($query);exit;
        $sql=self::$database->prepare($query);
        $sql->bindParam(":id",$this->id);
        $this->bindAttributes($sql);
        try{
            $sql->execute();
            $errorInfo = $sql->errorInfo();
            if(!$errorInfo){
                return false;
            }else{
            return true;
            }
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'.$sql->debugDumpParams());  
        }
    }

    public function save(){
        //var_dump($this);
        //se for um novo registo não vai existir id
        if(isset($this->id)){
            return $this->update();
        }else{ 
            return $this->create();
        }
    }

    public function merge_attributes($args=[]){
        foreach($args as $key=>$value){
            if(property_exists($this,$key) && !is_null($value)){
                $this->$key = $value;
            }
        }
    }
    // funçao que faz dinamicamente o bind dos parametros
    public function bindAttributes($sql){
        foreach(static::$db_columns as $column){
            if($column=='id'){continue;}
            $sql->bindParam(":".$column,$this->$column);
            $attributes[$column]=$this->$column;
        }
    }

    // função que vai ver dinamicamente as colunas da tabela
    public function attributes(){
        $attributes=[];
        foreach(static::$db_columns as $column){
            if($column=='id'){continue;}
            $attributes[$column]=$this->$column;
        }
        return $attributes;
    }

    public function delete(){
        $query="DELETE FROM ".static::$table_name." WHERE id=:id LIMIT 1";
        $sql=self::$database->prepare($query);
        $sql->bindParam(':id',$this->id);
        try{
            $sql->execute();
            $errorInfo = $sql->errorInfo();
            if(!$errorInfo){
                return false;
            } else{
                return true;
            }
        }catch(PDOException $e){
            die("Error on Database.".'<br/>'.$e->getMessage().'<br/><br/>'.$sql->debugDumpParams());  
        }
        //depois de apagar a instancia do objecto ainda existe
        // mesmo que na base de dados não exista
        //pode dar jeito para echo $user->first_name . "was deleted.";
    }

    public function safeFile(){
        $headers=static::$db_columns;
        $filename=SAFE_FILES_PATH . '/safeFile.csv';
        //fopen com a função w cria o ficheiro caso nao exista    

        if(!file_exists($filename)){
            $fp = fopen($filename, 'w');
            //colocamos só uma vez os headers
            fputcsv($fp, $headers);
        }else{
            $fp = fopen($filename, 'a');
        }
        $body=[];
        //vamos percorrer quantas posições o array tem
        //uma vez que nas validações vamos por os campos que nao vierem com '' 
        //todos os arrays que vem no $_POST tem as mesmas posições. 
        foreach (static::$db_columns as $value) {
            $body[].=$this->$value;
        }
            
        fputcsv($fp, $body);
        //fechamos o ficheiro
        fclose($fp);
    }
    /* FIM DO CÓDIGO DO PADRÃO DE DESENHO: ACTIVE RECORD */

}
?>