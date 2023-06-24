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
            $clients = $clientService->findAll();
            return(json_encode($clients));
        } catch (Exception $th) {
            return $th->getMessage();
        }
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

    public function destroy($id){
        try {
            $clientService = new ClientService();
            $response = $clientService->delete($id);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $response;

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
