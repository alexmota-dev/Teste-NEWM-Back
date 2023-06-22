<?php

namespace Database\Conection;
use PDO;

define('HOST', 'localhost');
define('DATABASENAME', 'terceirotestenewm');
define('USER', 'root');
define('PASSWORD', 'root');

class Connect {
   public $connection;

   function __construct() {
      $this->connectDatabase();
   }

   function connectDatabase() {
        try {
            $this->connection = new PDO('pgsql:host='.HOST.';dbname='.DATABASENAME, USER, PASSWORD);
        }
        catch (\PDOException $pdoError) {
            throw $pdoError;
        }
    }
}

$testConnection = new Connect();

?>
