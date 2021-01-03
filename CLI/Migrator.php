<?php
namespace glockmvc\core\\CLI;
use glockmvc\core\\Application;
class Migrator {
    public $dotenv;
    public $config;
    public $app;
    public function __construct($path)
    {
      $this->dotenv = \Dotenv\Dotenv::createImmutable($path);
      $this->dotenv->load();
      $this->config = [
        'userClass' => app\models\User::class,
        'db' => [
          'NAME' => $_ENV['DB_NAME'],
          'DSN' => $_ENV['DB_DSN'],
          'USER' => $_ENV['DB_USER'],
          'PASSWORD' => $_ENV['DB_PASSWORD']
        ]
      ];
      $this->app = new Application($path, $this->config);
    }
    function migrate() {
      $this->app->db->applyMigrations();
    }
    function migrateFresh() {
      $this->app->db->dropAllTables();
      $this->app->db->reconnect();
      $this->app->db->applyMigrations();
    }
    function rollback() {
      $this->app->db->rollback();
    }
  }
?>