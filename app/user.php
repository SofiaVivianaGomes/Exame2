<?php 

// MODEL

namespace App;
use Vendor\DatabaseObject;

class User extends DatabaseObject
{
    protected static $table_name = 'users';
    protected static $db_columns = ['name', 'username', 'password', 'register', 'login'];

    public $id, $name, $username, $password;

    public static function log_user($username, $password) {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND password = :password");
        $query->bindParam(':username', $username);
        $query->bindParam(':password', $password);
            
        $object_array= static::find_by_sql($query);
        
        if (!empty($object_array)){ //quer dizer que há um user com esse username e pass

          //atualizar o parâmetro login
          $login = date("Y-m-d H:i:s", time());

          $query = self::$database->prepare("UPDATE ". static::$table_name." SET login = :login WHERE username = :username LIMIT 1");
          $query->bindParam(':login', $login);
          $query->bindParam(':username', $username);
          $query->execute();

          //$query2->debugDumpParams();
            
          //$object_array2= static::find_by_sql($query);

          //var_dump($object_array2);

          //retorna o primeiro elemento do inicio do array
          //return array_shift($object_array);
          return true;
        } else {
          return false;
        }
    }

    public static function check_valid($username, $date) {
      
        $query = self::$database->prepare("UPDATE ". static::$table_name." SET valid = :valid WHERE username = :username LIMIT 1");
        $query->bindParam(':valid', $valid);
        $query->bindParam(':username', $username);
        $query->execute();

        $query2 = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND valid = :valid");
        $query2->bindParam(':username', $username);
        $query2->bindParam(':valid', $valid);
            
        $object_array= static::find_by_sql($query2);

        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }

  }

  public static function alter_user($username, $new_password) {
    
    $query = self::$database->prepare("UPDATE ". static::$table_name." SET password = :password WHERE username = :username LIMIT 1");
    $query->bindParam(':password', $new_password);
    $query->bindParam(':username', $username);
    $query->execute();

    $query2 = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND password = :password");
    $query2->bindParam(':username', $username);
    $query2->bindParam(':password', $new_password);
        
    $object_array= static::find_by_sql($query2);

    if (!empty($object_array)){
      return true;
    } else {
      return false;
    }

  }

    public static function find_by_username($username) 
    {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username");
        $query->bindParam(':username', $username);
            
        $object_array= static::find_by_sql($query);

        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }
    }

}

?>