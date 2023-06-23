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
        $startConnection = new Connect;
        $query = "SELECT * FROM clients";
        $cmd = $startConnection->connection->query($query);
        //fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        //convertendo para obj
        $clients = $cmd->fetchAll(PDO::FETCH_OBJ);
        return(json_encode($clients));
    }

    public function verifyCPF($cpf){
            $cpf = str_replace([" ", "-", "."], "", $cpf);
            return $cpf;
    }
    public function store(){
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
                $response = array(
                    'status' => 400,
                    'message' => 'O mês não pode ultrapassar 12'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
            if($birthArray[2] > 31){
                http_response_code(400);
                $response = array(
                    'status' => 400,
                    'message' => 'O dia não pode ultrapassar 31'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
        }
        else{
            $response = array(
                'status' => 400,
                'message' => 'Faltam dados na requisição. Verifique os campos.'
            );
            $json_response = json_encode($response);
            return $json_response;
        }

        $startConnection = new Connect();
        $existisClient = $this->findByEmail($email);
        if($existisClient){
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este email !"
            );
            $json_response = json_encode($response);
            return $json_response;
        }
        //Validando cpf antes de verificar tamanho
        $cpf = $this->verifyCPF($cpf);
        //verifica o tamanho de todos os campos
        if(strlen($phone) > 11 || strlen($cpf) > 11 || strlen($name) > 50 || strlen($email) > 50 || strlen($address) > 50 || strlen($observation) > 300){
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 400,
                'message' => "Verifique o tamanho dos campos."
            );
            $json_response = json_encode($response);
            return $json_response;
        }
        $query = "INSERT INTO clients (name, birth, phone, cpf, email, address, observation) VALUES (:name, :birth, :phone, :cpf, :email, :address, :observation)";
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":name",$name);
        $cmd->bindValue(":birth",$birth);
        $cmd->bindValue(":phone",$phone);
        $cmd->bindValue(":cpf",$cpf);
        $cmd->bindValue(":email",$email);
        $cmd->bindValue(":address",$address);
        // PDO::PARAM_NULL indica que observation pode ser null
        $cmd->bindValue(":observation",$observation, PDO::PARAM_NULL);

        $cmd->execute();
        //fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $response = array(
            'status' => 200,
            'message' => "User criado."
        );
        $json_response = json_encode($response);
        return $json_response;
    }

    public function show($id){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE id = :id';
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":id",$id);
        $cmd->execute();
        // fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        if($client){
            http_response_code(200);
            $this->addHeaders();
            die(json_encode($client));
        }
        else{
            http_response_code(404);
            $this->addHeaders();
            $response = array(
                'status' => 404,
                'message' => "Não existe cliente com esse id."
            );
            $json_response = json_encode($response);
            return $json_response;
        }
    }

    public function findByEmail($email){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE email = :email';
        $cmd = $startConnection->connection->prepare($query);
        $cmd->bindValue(":email",$email);
        $cmd->execute();
        // fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        if($client){
            http_response_code(200);
            // $this->addHeaders();
            return $client;
        }
        else{
            return 0;
        }
    }

    public function findByCPF($cpf){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE cpf = :cpf';
        $cmd = $startConnection->connection->prepare($query);
        $cmd->bindValue(":cpf",$cpf);
        $cmd->execute();
        // fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $client = $cmd->fetch(PDO::FETCH_OBJ);
        if($client){
            http_response_code(200);
            return $client;
        }
        else{
            return 0;
        }
    }
    public function SearchFor10UsersByEmail($email){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE email LIKE :email LIMIT 10';
        $cmd = $startConnection->connection->prepare($query);

        $cmd->bindValue(":email",$email."%");
        $cmd->execute();
        // fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $client = $cmd->fetchAll(PDO::FETCH_OBJ);
        if($client){
            http_response_code(200);
            // $this->addHeaders();
            die(json_encode($client));
        }
        else{
            http_response_code(404);
            $response = array(
                'status' => 404,
                'message' => "Não existe cliente com o email" . $email
            );
            $json_response = json_encode($response);
            return $json_response;
        }
    }

    public function SearchFor10UsersByName($name){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE name LIKE :name LIMIT 10';
        $cmd = $startConnection->connection->prepare($query);
        $cmd->bindValue(":name",$name."%");
        $cmd->execute();
        // fechando conexão por uma questão de segurança
        $this->closeConnection($startConnection);
        $client = $cmd->fetchAll(PDO::FETCH_OBJ);
        if($client){
            http_response_code(200);
            die(json_encode($client));
        }
        else{
            http_response_code(404);
            die("Não existe cliente com o name ". $name);
        }
    }

    public function destroy($id){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE id = :id';
        $cmd = $startConnection->connection->prepare($query);
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $userExists = $cmd->fetch();
        $cmd = null;

        if($userExists){
            $cmd = $startConnection->connection->prepare("DELETE FROM clients WHERE id = :id");
            $cmd->bindValue(":id",$id);
            $cmd->execute();
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 200,
                'message' => "User deletado com sucesso."
            );
            $json_response = json_encode($response);
            return $json_response;
        }else {
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 200,
                'message' => "Não existe user com o id". $id
            );
            $json_response = json_encode($response);
            return $json_response;
        }
    }

    public function update($id){
        $startConnection = new Connect();
        $query = 'SELECT * FROM clients WHERE id = :id';
        $cmd = $startConnection->connection->prepare($query);
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $userExists = $cmd->fetch();
        if(!$userExists){
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 404,
                'message' => "Não existe cliente com o id ". $id
            );
            $json_response = json_encode($response);
            return $json_response;
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
                // fechando conexão por uma questão de segurança
                $this->closeConnection($startConnection);
                $response = array(
                    'status' => 400,
                    'message' => 'O mês não pode ultrapassar 12'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
            if($birthArray[2] > 31){
                // fechando conexão por uma questão de segurança
                $this->closeConnection($startConnection);
                $response = array(
                    'status' => 400,
                    'message' => 'O dia não pode ultrapassar 31'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
            $alreadyExistsUserWithEmail = $this->findByEmail($email);
            if($alreadyExistsUserWithEmail && $alreadyExistsUserWithEmail->email != $userExists["email"] ){
                // fechando conexão por uma questão de segurança
                $this->closeConnection($startConnection);
                $response = array(
                    'status' => 409,
                    'message' => 'Já existe user com este email !'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
            $cpf = $this->verifyCPF($cpf);
            $alreadyExistsUserWithCPF = $this->findByCPF($cpf);
            if($alreadyExistsUserWithCPF && $alreadyExistsUserWithCPF->cpf != $userExists["cpf"] ){
                // fechando conexão por uma questão de segurança
                $this->closeConnection($startConnection);
                $response = array(
                    'status' => 409,
                    'message' => 'Já existe user com este CPF !'
                );
                $json_response = json_encode($response);
                return $json_response;
            }
            //verifica o tamanho de todos os campos
            if(strlen($phone) > 11 || strlen($cpf) > 11 || strlen($name) > 50 || strlen($email) > 50 || strlen($address) > 50 || strlen($observation) > 300){
                // fechando conexão por uma questão de segurança
                $this->closeConnection($startConnection);
                $response = array(
                    'status' => 400,
                    'message' => 'Verifique o tamanho dos campos !'
                );
                $json_response = json_encode($response);
                return $json_response;

            }

            $cmd = $startConnection->connection->
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

            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            $response = array(
                'status' => 200,
                'message' => 'Cliente atualizado com sucesso.'
            );
            $json_response = json_encode($response);
            return $json_response;
        }else {
            // fechando conexão por uma questão de segurança
            $this->closeConnection($startConnection);
            http_response_code(400);
            $response = array(
                'status' => 200,
                'message' => 'Solicitação incorreta, corpo da requisição está ausente.'
            );
            $json_response = json_encode($response);
            return $json_response;
        }
    }
}
