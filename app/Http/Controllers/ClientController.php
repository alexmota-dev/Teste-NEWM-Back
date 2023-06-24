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
            $response = $clientService->save($client);
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
        $body = json_decode(file_get_contents('php://input'), true);
        try {
            $clientService = new ClientService();
            $client = $clientService->checkBodyIntegrity($body);
            $clientService->emailIsUnique($client['email']);
            $clientService->cpfIsUnique($client['cpf']);
            $clientService->validatesSize($client);
            $client["id"] = $id;
            $response = $clientService->save($client);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $response;
    }
}
?>
