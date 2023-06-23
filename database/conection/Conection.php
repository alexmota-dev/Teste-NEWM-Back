<?php

namespace Database\Conection;
use PDO;

define('HOST', 'localhost');
define('DATABASENAME', 'testenewm');
define('USER', 'root');
define('PASSWORD', 'root');

class Connect {
   public $connection;

   function __construct() {
      $this->connectDatabase();
   }

   function connectDatabase() {
        try {
            $this->connection = new PDO('mysql:dbname='.DATABASENAME.';host='.HOST, USER, PASSWORD);
        }
        catch (\PDOException $pdoError) {
            throw $pdoError;
        }
    }

    function closeConection(){
        $this->connection = null;
    }
}

$testConnection = new Connect();

?>
