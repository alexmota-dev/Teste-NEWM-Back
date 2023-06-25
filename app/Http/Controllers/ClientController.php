<?php

namespace App\Http\Controllers;
use App\Providers\Service\ClientService;
use Exception;
use PDO;

class ClientController extends Controller
{

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
            $clientService->emailIsUniqueCreate($client['email']);
            $clientService->cpfIsUniqueCreate($client['cpf']);
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
            $client = $clientService->findById($id);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $client;
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
            $clientService->emailIsUniqueUpdate($client['email'], $id);
            $clientService->cpfIsUniqueUpdate($client['cpf'],$id);
            $clientService->validatesSize($client);
            $client["id"] = $id;
            $response = $clientService->update($client);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $response;
    }
}
?>
