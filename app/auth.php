<?php 

// MODEL

namespace App;
use Vendor\DatabaseObject;

class Auth extends DatabaseObject
{
    protected static $table_name = 'auth';
    protected static $db_columns = ['username','hash','register_date'];

    public $id, $username, $hash;

    public static function add_hash($username, $hash, $date) 
    {
        $query = self::$database->prepare("INSERT INTO ". static::$table_name. "(username, hash, register_date) VALUES (:username, :hash, :register_date)");
        $query->bindParam(':username', $username);
        $query->bindParam(':hash', $hash);
        $query->bindParam(':register_date', $date);
        $query->execute();

        $query2 = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND hash = :hash AND register_date = :register_date");
        $query2->bindParam(':username', $username);
        $query2->bindParam(':hash', $hash);
        $query2->bindParam(':register_date', $date);

        $object_array = static::find_by_sql($query2);

        // Se há alguma informação, quer dizer que a query foi bem sucedida
        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }
    }

    public static function verify_username_hash($username, $hash)
    {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND hash = :hash");
        $query->bindParam(':username', $username);
        $query->bindParam(':hash', $hash);
            
        $object_array = static::find_by_sql($query);

        // Se há alguma informação, quer dizer que a query foi bem sucedida
        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }
    }

    public static function verify_date_hash($username, $hash)
    {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username = :username AND hash = :hash");
        $query->bindParam(':username', $username);
        $query->bindParam(':hash', $hash);

        $current_date = date("Y-m-d H:i:s", time());
            
        $object_array = static::find_by_sql($query);

        var_dump($object_array);
        exit;

        // Se há alguma informação, quer dizer que a query foi bem sucedida
        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }
    }
    
    /*
    public static function get_hashes() 
    {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name);
            
        $object_array= static::find_by_sql($query);

        if (!empty($object_array)){
          return json_encode($object_array);
        } else {
          return false;
        }
    }
    */

}

?>