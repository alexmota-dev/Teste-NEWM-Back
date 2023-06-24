<?php

namespace Database;
use Database\Conection\Connect;
use PDO;

class ClientRepository{

    public function createClient($client){
        $startConnection = new Connect();
        $query = "INSERT INTO clients (name, birth, phone, cpf, email, address, observation) VALUES (:name, :birth, :phone, :cpf, :email, :address, :observation)";
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":name",$client["name"]);
        $cmd->bindValue(":birth",$client["birth"]);
        $cmd->bindValue(":phone",$client["phone"]);
        $cmd->bindValue(":cpf",$client["cpf"]);
        $cmd->bindValue(":email",$client["email"]);
        $cmd->bindValue(":address",$client["address"]);
        // PDO::PARAM_NULL indica que observation pode ser null
        $cmd->bindValue(":observation",$client["observation"], PDO::PARAM_NULL);
        $cmd->execute();
        return $client;
    }

    public function findByAllClients(){
        $startConnection = new Connect;
        $query = "SELECT * FROM clients";
        $cmd = $startConnection->connection->query($query);

        //convertendo para obj
        $clients = $cmd->fetchAll(PDO::FETCH_OBJ);
        return $clients;
    }

    public function findById($id){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE id = :id';
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        return $client;
    }

    public function findByEmail($email){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE email = :email';
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":email",$email);
        $cmd->execute();
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        return $client;
    }

    public function findByCPF($cpf){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE cpf = :cpf';
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":cpf",$cpf);
        $cmd->execute();
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        return $client;
    }
}

?>