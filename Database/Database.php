<?php
namespace glockmvc\core\\Database;
use glockmvc\core\\Application;
class Database {
  public \PDO $pdo;
  protected string $dbName;
  protected string $user;
  protected string $dsn;
  protected string $password;
  public function __construct(array $config) {
    $this->dsn = $config['DSN'] ?? '';
    $this->user = $config['USER'] ?? '';
    $this->password = $config['PASSWORD'] ?? '';
    $this->dbName = $config['NAME'] ?? '';
    $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
  }
  public function reconnect() {
    $this->pdo = new \PDO($this->dsn, $this->user, $this->password);
    $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    $this->logMessage('Reconnection to the Database has been successful');
  }
  public function getLastMigrationRound() {
    $stmt = $this->pdo->prepare("SELECT round FROM migrations order by round desc limit 1");
    $stmt->execute();
    return intval($stmt->fetchColumn(0) ?? 0);
  }
  public function rollback(){
    $this->createMigrationTable();
    $lastRound = $this->getLastMigrationRound();
    $appliedMigrations = $this->getAppliedMigrationsByRound($lastRound);
    $files = scandir(Application::$ROOT_DIR.'/migrations');
    $toApplyMigrations = array_intersect($files, $appliedMigrations);
    foreach($toApplyMigrations as $migration) {
      if($migration === "." || $migration === "..") {
        continue;
      }
      require_once Application::$ROOT_DIR.'/migrations/'.$migration;
      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className();
      $this->logMessage('Rolling back Migration '.$className.'...');
      $instance->down();
      $this->logMessage('Migration Rollbacked! ');
      $newMigrations[] = $migration;
    }
    if (!empty($newMigrations)){
      $this->removeMigration($newMigrations);
    }
    else {
         $this->logMessage("There is no migration executed!");
    }
  }
  public function applyMigrations() {
    $this->createMigrationTable();
    $appliedMigrations = $this->getAppliedMigrations();
    $newMigrations = [];
    $files = scandir(Application::$ROOT_DIR.'/migrations');
    $toApplyMigrations = array_diff($files, $appliedMigrations);
    foreach($toApplyMigrations as $migration) {
      if($migration === "." || $migration === "..") {
        continue;
      }
      require_once Application::$ROOT_DIR.'/migrations/'.$migration;
      $className = pathinfo($migration, PATHINFO_FILENAME);
      $instance = new $className();
      $this->logMessage('Applying Migration '.$className.'...');
      $instance->up();
      $this->logMessage('Migration Applied! ');
      $newMigrations[] = $migration;
    }
    if (!empty($newMigrations)){
      $this->saveMigrations($newMigrations, $this->getLastMigrationRound()+1);
    }
    else {
         $this->logMessage("All migrations are already applied!");
    }
  }

  public function createMigrationTable() {
    $this->pdo->exec("CREATE TABLE IF NOT EXISTS migrations (
      id INT AUTO_INCREMENT PRIMARY KEY,
      migration VARCHAR(255),
      round int NOT NULL,
      created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP)
      ENGINE=INNODB;
    ");
  }
  public function dropAllTables() {
      $dbName = $this->dbName;
      $this->pdo->exec("DROP DATABASE $dbName; CREATE DATABASE $dbName");
      $this->logMessage("Successfully dropped the Database $dbName!");
  }
  public function getAppliedMigrations() {
    $statement = $this->pdo->prepare("SELECT migration FROM migrations");
    $statement->execute();
    return $statement->fetchAll(\PDO::FETCH_COLUMN);
  }
  public function getAppliedMigrationsByRound($round) {
    $statement = $this->pdo->prepare("SELECT migration FROM migrations where round=$round");
    $statement->execute();
    return $statement->fetchAll(\PDO::FETCH_COLUMN);
  }
  public function saveMigrations(array $migrations, int $lastRound) {
      $str = implode(",",array_map(fn($m) => "('$m', $lastRound)",$migrations));
      $statement = $this->pdo->prepare("INSERT INTO migrations(migration, round) values $str");
      $statement->execute();
      $this->logMessage('All the migration have been applied!');
  }
  public function removeMigration(array $migration) {
    $str = implode(" OR ",array_map(fn($m) => "migration = '$m'",$migration));
    $statement = $this->pdo->prepare("DELETE FROM migrations WHERE $str");
    $statement->execute();
    $this->logMessage("Rollback completed!");
  }
  public function logMessage($message){
       echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
 }
 public function prepare($SQL) {
      return $this->pdo->prepare($SQL);
 }
 public function execute(string $sql){
    return $this->pdo->exec($sql);
 }
}
 ?>
