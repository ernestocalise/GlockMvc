<?php
namespace glockmvc\core\Database;
abstract class Migration {
    public string $tableName;
    abstract public function up();
    abstract public function down();
    public static function createTable(string $tableName){
        $table = new Table($tableName);
        return $table;
    }
    public static function dropTableIfExists($tableName){
      return "DROP TABLE IF EXISTS $tableName";
    }
    
}




?>