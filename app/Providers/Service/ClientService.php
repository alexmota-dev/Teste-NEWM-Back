<?php

namespace App\Providers\Service;
require_once '../Database/ClientRepository.php';
use Database\ClientRepository;
use Exception;
use PDO;

class ClientService extends Exception{

    public function checkBodyIntegrity($body){
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
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                throw new Exception($json_response);
            }
            if($birthArray[2] > 31){
                http_response_code(400);
                $response = array(
                    'status' => 400,
                    'message' => 'O dia não pode ultrapassar 31'
                );
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                throw new Exception($json_response);
            }
        }
        else{
            $response = array(
                'status' => 400,
                'message' => 'Faltam dados na requisição. Verifique os campos.'
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    //formatando cpf antes de enviar
    $cpf = $this->formatCPF($cpf);
    $client["name"] = $name;
    $client["birth"] = $birth;
    $client["phone"] = $phone;
    $client["cpf"] = $cpf;
    $client["email"] = $email;
    $client["address"] = $address;
    $client["observation"] = $observation;

    return $client;
    }

    public function emailIsUnique($email){
        $clientWithSameEmail = $this->findByEmail($email);
        if($clientWithSameEmail){
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este email !"
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function cpfIsUnique($cpf){
        $clientWithSameCPF = $this->findByCPF($cpf);
        if($clientWithSameCPF){
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este CPF !"
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function validatesSize($client){
        //verifica o tamanho de todos os campos
        if(strlen($client["phone"]) > 11 || strlen($client["cpf"]) > 11 || strlen($client["name"]) > 50 || strlen($client["email"]) > 50 || strlen($client["address"]) > 50 || strlen($client["observation"]) > 300){
            $response = array(
                'status' => 400,
                'message' => "Verifique o tamanho dos campos."
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function findById($id){
        $database = new ClientRepository();
        $client = $database->findById($id);
        if($client){
            return $client;
        }
        else{
            $response = array(
                'status' => 404,
                'message' => "Não existe cliente com esse id."
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function findByEmail($email){
        $database = new ClientRepository();
        $client = $database->findByEmail($email);
        if($client){
            return $client;
        }
        else{
            /*Não posso retornar uma response aqui porque essa function só é usada aqui
            logo seu valor é $client ou 0 que seria equivalente a false*/
            return 0;
        }
    }

    public function findByCPF($cpf){
        $database = new ClientRepository();
        $client = $database->findByCPF($cpf);
        if($client){
            return $client;
        }
        else{
            /*Não posso retornar uma response aqui porque essa function só é usada aqui
            logo seu valor é $client ou 0 que seria equivalente a false*/
            return 0;
        }
    }


    public function formatCPF($cpf){
        $cpf = str_replace([" ", "-", "."], "", $cpf);
        return $cpf;
    }

    public function save($client){
        try {
            $clientDatabase = new ClientRepository();
            if(isset($client["id"])){
                $existisClientWithSameId = $clientDatabase->findById($client["id"]);
                if($existisClientWithSameId){
                    //update
                    $clientDatabase->updateClient($client);
                    $response = array(
                        'status' => 200,
                        'message' => 'Cliente atualizado com sucesso.'
                    );
                    $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
                    $json_response;
                }

            }
            else{
                //create
                $clientDatabase->createClient($client);
                $response = array(
                    'status' => 200,
                    'message' => "User criado."
                );
                $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $json_response;
    }

    public function findAllClients(){
        try {
            $clientDatabase = new ClientRepository();
            $clients = $clientDatabase->findByAllClients();
            return $clients;
        } catch (Exception $th) {
            return $th;
        }
    }
}
?>
