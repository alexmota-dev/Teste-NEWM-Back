<?php

namespace App\Http\Controllers;
require_once '../Database/Conection/Conection.php';
use Database\Conection\Connect;
use PDO;

class ClientController extends Controller
{
    public function closeConnection($pdo){
        $pdo = NULL;
    }
    //
    public function addHeaders(){
        //função para evitar erro de CORS
        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
        header('Access-Control-Allow-Headers: Content-Type');
    }
    public function index(){
        //abrindo a conexão com o banco
        $testConnection = new Connect;
        // $cmd = $testConnection->connection->query($query);
        //selecionando todos os funcionairos
        $query = "SELECT * FROM clients";
        $cmd = $testConnection->connection->query($query);
        //convertendo para obj
        $clients = $cmd->fetchAll(PDO::FETCH_OBJ);
        //fechando a conexão com o banco
        // $this->closeConnection($testConnection);
        // return $clients;
        //qual a dirença exata de usar um return ou die ?
        // $this->addHeaders();
        return(json_encode($clients));
    }

    public function verifyCPF($cpf){
            $cpf = str_replace([" ", "-", "."], "", $cpf);
            return $cpf;
    }
    public function store(){
        // $body = (json_decode(file_get_contents('php://input'),false))->body;
        $body = json_decode(file_get_contents('php://input'), true);
        if(isset($body['observation'])){
            $observation = $body["observation"];
        }
        else{
            $observation = null;
        }
        if(isset($body['name']) && isset($body['birth']) && isset($body['phone']) && isset($body['cpf']) && isset($body['email']) && isset($body['address'])){
            $name = $body["name"];
            $birth = $body["birth"];
            $phone = $body["phone"];
            $cpf = $body["cpf"];
            $email = $body["email"];
            $address = $body["address"];
            $birthArray = explode("-",$birth);
            if($birthArray[1] > 12){
                http_response_code(400);
                // $this->addHeaders();
                die("O mês não pode ultrapassar 12");
            }
            if($birthArray[2] > 31){
                http_response_code(400);
                // $this->addHeaders();
                die("O dia não pode ultrapassar 31");
            }
        }
        else{
            http_response_code(400);
            die("Faltam dados na requisição. Verifique os campos.");
        }
        if(strlen($phone) > 11 || strlen($cpf) > 11){
            http_response_code(400);
            die("Verifique se os campos CPF/Telefone tem um tamanho adequado.");
        }

        $pdo = new Connect();
        $existisFuncionario = $this->findByEmail($email);
        if($existisFuncionario){
            http_response_code(409);
            die("Existe funcionario com o email ". $email);
        }
        $query = "INSERT INTO clients (name, birth, phone, cpf, email, address, observation) VALUES (:name, :birth, :phone, :cpf, :email, :address, :observation)";
        $cmd = $pdo->connection->prepare($query);

        $cmd->bindValue(":name",$name);
        $cmd->bindValue(":birth",$birth);
        $cmd->bindValue(":phone",$phone);
        $cmd->bindValue(":cpf",$cpf);
        $cmd->bindValue(":email",$email);
        $cmd->bindValue(":address",$address);
        // PDO::PARAM_NULL indica que observation pode ser null
        $cmd->bindValue(":observation",$observation, PDO::PARAM_NULL);

        $cmd->execute();

        $this->closeConnection($pdo);
        http_response_code(200);
        // $this->addHeaders();
        die("User criado !");
    }

    public function show($id){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        // esse trecho se repete no show e no destroy
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE id = :id');
        // SELECT * FROM clients WHERE name LIKE CONCAT(:name, '%');
        // ^^^^^^
        // na busca pelo name eu posso usar isso

        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $funcionario = $cmd->fetch(PDO::FETCH_OBJ);
        if($funcionario){
            $this->closeConnection($pdo);
            http_response_code(200);
            $this->addHeaders();
            die(json_encode($funcionario));
        }
        else{
            $this->closeConnection($pdo);
            http_response_code(404);
            $this->addHeaders();
            die("Não existe funcionario com o id ". $id);
        }
        //consigo acessar $funcionario->name, formato: Obj.
    }

    public function findByEmail($email){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        // esse trecho se repete no show e no destroy
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE email = :email');
        // SELECT * FROM clients WHERE name LIKE CONCAT(:name, '%');
        // na busca pelo name eu posso usar isso
        $cmd->bindValue(":email",$email);
        $cmd->execute();
        $funcionario = $cmd->fetch(PDO::FETCH_OBJ);
        if($funcionario){
            $this->closeConnection($pdo);
            http_response_code(200);
            // $this->addHeaders();
            return $funcionario;
        }
        else{
            $this->closeConnection($pdo);
            http_response_code(404);
            // $this->addHeaders();
            return 0;
        }
        //consigo acessar $funcionario->name, formato: Obj.
    }

    public function SearchFor10UsersByEmail($email){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        // esse trecho se repete no show e no destroy
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE email LIKE :email LIMIT 10');
        // SELECT * FROM clients WHERE name LIKE CONCAT(:name, '%');
        // ^^^^^^
        // na busca pelo name eu posso usar isso
        $cmd->bindValue(":email",$email."%");
        $cmd->execute();
        $funcionario = $cmd->fetchAll(PDO::FETCH_OBJ);
        if($funcionario){
            $this->closeConnection($pdo);
            http_response_code(200);
            // $this->addHeaders();
            die(json_encode($funcionario));
        }
        else{
            $this->closeConnection($pdo);
            http_response_code(404);
            // $this->addHeaders();
            die("Não existe funcionario com o email ". $email);
        }
        //consigo acessar $funcionario->name, formato: Obj.
    }

    public function SearchFor10UsersByName($name){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        // esse trecho se repete no show e no destroy
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE name LIKE :name LIMIT 10');
        // SELECT * FROM clients WHERE name LIKE CONCAT(:name, '%');
        // ^^^^^^
        // na busca pelo name eu posso usar isso
        $cmd->bindValue(":name",$name."%");
        $cmd->execute();
        $funcionario = $cmd->fetchAll(PDO::FETCH_OBJ);
        if($funcionario){
            $this->closeConnection($pdo);
            http_response_code(200);
            // $this->addHeaders();
            die(json_encode($funcionario));
        }
        else{
            $this->closeConnection($pdo);
            http_response_code(404);
            // $this->addHeaders();
            die("Não existe funcionario com o name ". $name);
        }
        //consigo acessar $funcionario->name, formato: Obj.
    }

    public function destroy($id){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=127.0.0.1', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE id = :id');
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $userExists = $cmd->fetch();
        $cmd = null;

        if($userExists){
            $cmd = $pdo->prepare("DELETE FROM clients WHERE id = :id");
            $cmd->bindValue(":id",$id);
            $cmd->execute();
            $this->closeConnection($pdo);
            // $this->addHeaders();
            die("O funcionario de id ".$id." foi removido.");
        }else {
            //posso fazer uma função pra executar essas 3 linhas abaixo, elas se repetem
            $this->closeConnection($pdo);
            http_response_code(404);
            // $this->addHeaders();
            die("Não existe funcionario com o id ". $id);
        }
    }

    public function update($id){
        try {
            $pdo = new PDO('mysql:dbname=terceirotestenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        $cmd = $pdo->prepare('SELECT * FROM clients WHERE id = :id');
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $userExists = $cmd->fetch();
        $cmd = null;
        if(!$userExists){
            $this->closeConnection($pdo);
            http_response_code(404);
            $this->addHeaders();
            die("Não existe funcionario com o id ". $id);
        }

        $jsonObj = json_decode(file_get_contents('php://input'), true);

        if($jsonObj){
            $name = $jsonObj["name"];
            $birth = $jsonObj["birth"];
            $phone = $jsonObj["phone"];
            $cpf = $jsonObj["cpf"];
            $email = $jsonObj["email"];
            $address = $jsonObj["address"];
            $observation = $jsonObj["observation"];
            $birthArray = explode("-",$birth);

            if($birthArray[1] > 12){
                $this->closeConnection($pdo);
                http_response_code(400);
                // $this->addHeaders();
                die("O mês não pode ultrapassar 12");
            }
            if($birthArray[2] > 31){
                $this->closeConnection($pdo);
                http_response_code(400);
                // $this->addHeaders();
                die("O dia não pode ultrapassar 31");
            }
            $alreadyExistsUserWithEmail = $this->findByEmail($email);
            if($alreadyExistsUserWithEmail && $alreadyExistsUserWithEmail->email != $userExists["email"] ){
                http_response_code(409);
                die("Já existe user com este email !");
            }
            $cpf = $this->verifyCPF($cpf);
            var_dump($cpf);
            $cmd = $pdo->
            prepare(
                    "UPDATE clients
                    SET name = :name, birth = :birth, phone = :phone, cpf = :cpf, email = :email, address = :address, observation = :observation
                    WHERE id = :id"
                    );

            $cmd->bindValue(":name",$name);
            $cmd->bindValue(":birth",$birth);
            $cmd->bindValue(":phone",$phone);
            $cmd->bindValue(":cpf",$cpf);
            $cmd->bindValue(":email",$email);
            $cmd->bindValue(":address",$address);
            $cmd->bindValue(":observation",$observation);
            $cmd->bindValue(":id",$id);
            $cmd->execute();

            $this->closeConnection($pdo);
            http_response_code(200);
            $this->addHeaders();
            die("Funcionario atualizado com sucesso.");
        }else {
            $this->closeConnection($pdo);
                http_response_code(400);
                $this->addHeaders();
                die("Solicitação incorreta, corpo da requisição está errado.");
        }
    }
}
