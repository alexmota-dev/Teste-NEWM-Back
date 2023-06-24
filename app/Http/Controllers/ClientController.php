<?php

namespace App\Http\Controllers;
require_once '../Database/Conection/Conection.php';
use App\Providers\Service\ClientService;
use Database\Conection\Connect;
use Exception;
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
        try {
            $clientService = new ClientService();
            $clients = $clientService->findAllClients();
            return(json_encode($clients));
        } catch (Exception $th) {
            return $th->getMessage();
        }
    }

    public function verifyCPF($cpf){
        $cpf = str_replace([" ", "-", "."], "", $cpf);
        return $cpf;
    }
    public function store(){
        $body = json_decode(file_get_contents('php://input'), true);
        try {
            $clientService = new ClientService();
            $client = $clientService->checkBodyIntegrity($body);
            $clientService->emailIsUnique($client['email']);
            $clientService->cpfIsUnique($client['cpf']);
            $clientService->validatesSize($client);
            $response = $clientService->createClient($client);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $response;
    }

    public function show($id){
        try {
            $clientService = new ClientService();
            $clientService->findById($id);
        } catch (Exception $th) {
            return $th->getMessage();
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
?>
