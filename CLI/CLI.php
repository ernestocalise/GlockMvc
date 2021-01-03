<?php
namespace ernestocalise\glockmvc\CLI;
class CLI{
    public string $path;
    public function __construct($path)
    {
      $this->path = $path;
    }
    public function logMessage($message){
      echo '['.date('Y-m-d H:i:s').'] - '.$message.PHP_EOL;
    }
    public function info(){
      echo PHP_EOL.'GlockMVC CLI v 1.0.0.0'.PHP_EOL.PHP_EOL.
      'COMMAND LIST '.PHP_EOL.PHP_EOL.
      '----------------------------------------------'.PHP_EOL.
      'database:migrate                 :::::::  Run all the migration '.PHP_EOL.
      'database:migrateOne              :::::::  Run all the migration '.PHP_EOL.
      'database:fresh                   :::::::  drop all the tables and run migrations'.PHP_EOL.
      'database:rollback                :::::::  Cancel the last migration round '.PHP_EOL.
      'make:migration                   :::::::  creates a migration file'.PHP_EOL.
      'make:migrationTable              :::::::  creates and initialize a migration file to create a table'.PHP_EOL.
      'make:model                       :::::::  creates a  model file'.PHP_EOL.
      'make:dbModelAndMigration         :::::::  creates a model and the related migration file'.PHP_EOL.
      'info                             :::::::  List all the commands '.PHP_EOL.
      'serve                            :::::::  Start the development server'.PHP_EOL.PHP_EOL;
    }
    public function migrator(){
      return new Migrator($this->path);
    }
    public function Filebuilder(){
      return new FileBuilder($this->path);
    }
    public function serve() {
      chdir("public");
      $this->logMessage('Started GlockMVC Development Server on localhost:8000');
      passthru(PHP_BINARY." -S localhost:8000");
    }
}