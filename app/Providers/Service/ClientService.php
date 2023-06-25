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

    public function emailIsUniqueUpdate($email,$id){
        $idClient = $this->findIdByEmail($email);
        if($idClient != $id && $idClient){
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este email !"
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function emailIsUniqueCreate($email){
        $id = $this->findIdByEmail($email);
        if($id){
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este email !"
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function cpfIsUniqueUpdate($cpf,$id){
        $idClient = $this->findIdByCPF($cpf);
        if($idClient != $id && $idClient ){
            $response = array(
                'status' => 409,
                'message' => "Já existe um cliente com este CPF !"
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
            throw new Exception($json_response);
        }
    }

    public function cpfIsUniqueCreate($cpf){
        $id = $this->findIdByCPF($cpf);
        if($id){
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
        $clientDatabase = new ClientRepository();
        $client = $clientDatabase->findById($id);
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

    public function findIdByEmail($email){
        $clientDatabase = new ClientRepository();
        $id = $clientDatabase->findIdByEmail($email);
        if($id){
            return $id;
        }
        else{
            /*Não posso retornar uma response aqui porque essa function só é usada aqui
            logo seu valor é $client ou 0 que seria equivalente a false*/
            return 0;
        }
    }

    public function findIdByCPF($cpf){
        $clientDatabase = new ClientRepository();
        $id = $clientDatabase->findIdByCPF($cpf);
        if($id){
            return $id;
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
            $clientDatabase->create($client);
            $response = array(
                'status' => 200,
                'message' => "Cliente criado com sucesso."
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (Exception $th) {
            return $th->getMessage();
        }
        return $json_response;
    }

    public function update($client){
        try {
            $clientDatabase = new ClientRepository();
            $clientDatabase->update($client);
            $response = array(
                'status' => 200,
                'message' => 'Cliente atualizado com sucesso.'
            );
            $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
        return $json_response;
    }

    public function delete($id){
        try {
            $clientDatabase = new ClientRepository();
            $clientExistis = $clientDatabase->findById($id);
            if($clientExistis){
                $clientDatabase->delete($id);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        $response = array(
            'status' => 200,
            'message' => "User deletado com sucesso."
        );
        $json_response = json_encode($response, JSON_UNESCAPED_UNICODE);
        return $json_response;
    }

    public function findAll(){
        try {
            $clientDatabase = new ClientRepository();
            $clients = $clientDatabase->findByAll();
            return $clients;
        } catch (Exception $th) {
            return $th;
        }
    }
}
?>
