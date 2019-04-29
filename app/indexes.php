<?php 

// MODEL

namespace App;
use Vendor\DatabaseObject;

class Indexes extends DatabaseObject
{
    protected static $table_name = 'indexes';
    protected static $db_columns = ['symbol', 'type', 'trading_hours', 'description', 'spread_target_standard'];

    private $id, $symbol, $type, $trading_hours, $description, $spread_target_standard;

    public function set_properties($prop) {
      /*
      echo "<pre>";
      var_dump($prop);
      echo "</pre>"; 
      */

      $this->symbol = h($prop[0]);
      $this->type = h($prop[1]);
      $this->trading_hours = h($prop[2]);
      $this->description = h($prop[3]);
      $this->spread_target_standard = h($prop[4]);
    }

    public static function get_indexes() {
        $query = self::$database->prepare("SELECT * FROM ".static::$table_name);
            
        $object_array = static::find_by_sql($query);
        
        // Se há alguma informação, quer dizer que a query foi bem sucedida
        if (!empty($object_array)){ 
          return json_encode($object_array);
        } else {
          return false;
        }
    }

    public static function get_symbols_ids() {
      $query = self::$database->prepare("SELECT id, symbol FROM ".static::$table_name);
          
      $object_array = static::find_by_sql($query);
      
      // Se há alguma informação, quer dizer que a query foi bem sucedida
      if (!empty($object_array)){ 
        return json_encode($object_array);
      } else {
        return false;
      }
  }

}


?>