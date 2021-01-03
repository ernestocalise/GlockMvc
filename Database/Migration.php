<?php
/*
class m0001_create_table_users {
  public function up(){
    $db = \ernestocalise\glockmvc\Application::$app->db;
    $SQL = "CREATE TABLE users (
         id INT AUTO_INCREMENT PRIMARY KEY,
         email VARCHAR(255) NOT NULL,
         firstname VARCHAR(255) NOT NULL,
         lastname VARCHAR(255) NOT NULL,
         status TINYINT NOT NULL,
         created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=INNODB;";
    $db->pdo->exec($SQL);
  }
  public function down() {
       $db = \ernestocalise\glockmvc\Application::$app->db;
       $SQL = "DROP TABLE users;";
       $db->pdo->exec($SQL);
  }
}
*/
namespace ernestocalise\glockmvc\Database;
abstract class Migration {
    public string $tableName;
    //abstract public function up();
    //abstract public function down();
    public static function createTable(string $tableName){
        $table = new Table($tableName);
        return $table;
    }
    public static function dropTableIfExists($tableName){
      return "DROP TABLE IF EXISTS $tableName";
    }
    
}




?>