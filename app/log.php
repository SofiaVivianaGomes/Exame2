<?php 

// MODEL

namespace App;
use Vendor\DatabaseObject;

class Log extends DatabaseObject
{
    protected static $table_name = 'logs';
    protected static $db_columns = ['username_id', 'date', 'request', 'ip'];

    public $id, $username_id, $date, $request, $ip;

    public static function add_log($data) 
    {
        $query = self::$database->prepare("INSERT INTO ". static::$table_name. "(username_id, date, request, ip) VALUES (:username_id, :date, :request, :ip)");
        $query->bindParam(':username_id', $data['username_id']);
        $query->bindParam(':date', $data['date']);
        $query->bindParam(':request', $data['request']);
        $query->bindParam(':ip', $data['ip']);
        $query->execute();

        $query2 = self::$database->prepare("SELECT * FROM ".static::$table_name." WHERE username_id = :username_id AND date = :date AND request = :request AND ip = :ip");
        $query2->bindParam(':username_id', $data['username_id']);
        $query2->bindParam(':date', $data['date']);
        $query2->bindParam(':request', $data['request']);
        $query2->bindParam(':ip', $data['ip']);

        $object_array = static::find_by_sql($query2);

        // Se há alguma informação, quer dizer que a query foi bem sucedida
        if (!empty($object_array)){
          return true;
        } else {
          return false;
        }
    }
    
    public static function all_logs() 
    {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name);
            
        $object_array = static::find_by_sql($query);

        if (!empty($object_array)){
          return json_encode($object_array);
        }else{
          return false;
        }
    }

}

?>