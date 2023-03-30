<?php

namespace App\Http\Controllers;
use PDO;

class EmployeeController extends Controller
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
        $pdo = new PDO('mysql:dbname=testenewm; host=localhost', "root", "root");
        //selecionando todos os funcionairos
        $cmd = $pdo->query("SELECT * FROM funcionarios");
        //convertendo para obj
        $funcionarios = $cmd->fetchAll(PDO::FETCH_OBJ);
        //fechando a conexão com o banco
        $this->closeConnection($pdo);
        http_response_code(200);
        // return $funcionarios;
        //Ao usar o die eu tenho erro de CORS não entendi muito bem, mas acredito que
        // o return por ser mais comum deve receber um header automatico, enquanto o die
        // não deve passar header algum
        $this->addHeaders();
        die(json_encode($funcionarios));
    }

    public function store(){
        // $body = (json_decode(file_get_contents('php://input'),false))->body;
        $body = json_decode(file_get_contents('php://input'), true);

        $nome = $body["nome"];
        $nascimento = $body["nascimento"];
        $celular = $body["celular"];
        $cpf = $body["cpf"];
        $email = $body["email"];
        $endereco = $body["endereco"];
        $observacao = $body["observacao"];
        $nascimentoArray = explode("-",$nascimento);

        if($nascimentoArray[1] > 12){
            http_response_code(400);
            $this->addHeaders();
            die("O mês não pode ultrapassar 12");
        }
        if($nascimentoArray[2] > 31){
            http_response_code(400);
            $this->addHeaders();
            die("O dia não pode ultrapassar 31");
        }

        try {
            $pdo = new PDO('mysql:dbname=testenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }

        $cmd = $pdo->prepare("INSERT INTO funcionarios (nome, nascimento, celular, cpf, email, endereco, observacao) VALUES (:nome, :nascimento, :celular, :cpf, :email, :endereco, :observacao)");
        $cmd->bindValue(":nome",$nome);
        $cmd->bindValue(":nascimento",$nascimento);
        $cmd->bindValue(":celular",$celular);
        $cmd->bindValue(":cpf",$cpf);
        $cmd->bindValue(":email",$email);
        $cmd->bindValue(":endereco",$endereco);
        $cmd->bindValue(":observacao",$observacao);

        $cmd->execute();

        $this->closeConnection($pdo);
        http_response_code(200);
        $this->addHeaders();
        die("User criado !");
    }

    public function show($id){
        try {
            $pdo = new PDO('mysql:dbname=testenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        //esse trecho se repete no show e no destroy
        $cmd = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
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
        //consigo acessar $funcionario->nome, formato: Obj.
    }

    public function destroy($id){
        try {
            $pdo = new PDO('mysql:dbname=testenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        $cmd = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
        $cmd->bindValue(":id",$id);
        $cmd->execute();
        $userExists = $cmd->fetch();
        $cmd = null;

        if($userExists){
            $cmd = $pdo->prepare("DELETE FROM funcionarios WHERE id = :id");
            $cmd->bindValue(":id",$id);
            $cmd->execute();
            $this->closeConnection($pdo);
            $this->addHeaders();
            die("O funcionario de id ".$id." foi removido.");
        }else {
            //posso fazer uma função pra executar essas 3 linhas abaixo, elas se repetem
            $this->closeConnection($pdo);
            http_response_code(404);
            $this->addHeaders();
            die("Não existe funcionario com o id ". $id);
        }
    }

    public function update($id){
        try {
            $pdo = new PDO('mysql:dbname=testenewm; host=localhost', "root", "root");
        } catch (\PDOException $pdoError) {
            throw $pdoError;
        }
        $cmd = $pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
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
            $nome = $jsonObj["nome"];
            $nascimento = $jsonObj["nascimento"];
            $celular = $jsonObj["celular"];
            $cpf = $jsonObj["cpf"];
            $email = $jsonObj["email"];
            $endereco = $jsonObj["endereco"];
            $observacao = $jsonObj["observacao"];
            $nascimentoArray = explode("-",$nascimento);

            if($nascimentoArray[1] > 12){
                $this->closeConnection($pdo);
                http_response_code(400);
                $this->addHeaders();
                die("O mês não pode ultrapassar 12");
            }
            if($nascimentoArray[2] > 31){
                $this->closeConnection($pdo);
                http_response_code(400);
                $this->addHeaders();
                die("O dia não pode ultrapassar 31");
            }

            $cmd = $pdo->
            prepare(
                    "UPDATE funcionarios
                    SET nome = :nome, nascimento = :nascimento, celular = :celular, cpf = :cpf, email = :email, endereco = :endereco, observacao = :observacao
                    WHERE id = :id"
                    );

            $cmd->bindValue(":nome",$nome);
            $cmd->bindValue(":nascimento",$nascimento);
            $cmd->bindValue(":celular",$celular);
            $cmd->bindValue(":cpf",$cpf);
            $cmd->bindValue(":email",$email);
            $cmd->bindValue(":endereco",$endereco);
            $cmd->bindValue(":observacao",$observacao);
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
