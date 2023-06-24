<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Atualizar o projeto
```bash
composer install
```
### Antes de continuar, você tem o docker instalado ? Se tiver siga a proxima instrução, caso não tenha o docker mas tenha o mysql então ignore o comando docker.
### Caso você tenha o docker
#### Use o comando a baixo para criar um container mysql para uso na aplicação, no meu caso, eu usei o docker, não tenho o mysql instalado localmente.
```bash
docker run --name meu-mysql -e MYSQL_ROOT_PASSWORD=root -p 3306:3306 -d mysql
```
#### Dessa forma o MYSQL_ROOT_PASSWORD define a senha como "root" e o user é setado como "root" de forma automatica
#### Com o mysql rodando, seja pelo docker ou localmente, basta utilizar o gerenciador de DB de sua preferencia, no meu caso eu uso o beekeeper, insira as credenciais do banco:<br/> Host: localhost<br/> Password: root <br/> User: root <br/> Port: 3306 <br/> e insira os codigos SQL:
#### Criando Banco:
```bash
CREATE DATABASE testenewm;
```

#### Criando Tabela clients:
```bash
DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` BIGINT(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` VARCHAR(50) UNIQUE COLLATE utf8mb4_unicode_ci NOT NULL,
  `observation` VARCHAR(300) COLLATE utf8mb4_unicode_ci,
  `address` VARCHAR(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpf` VARCHAR(11) NOT NULL,
    -- O date tem a data em um formato diferente do usado no BR -> "YYYY-MM-DD"
  `birth` DATE NOT NULL,
  `phone` VARCHAR(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### Insira dados ao banco:
##### Para fazer testes, o banco precisa de dados, mas você pode fazer todas inserções de clients manualmente pelo frontend da aplicação.
```bash
# Inserindo dados no banco para testes.
INSERT INTO `clients` (`name`, `email`, `observation`, `address`, `cpf`, `birth`, `phone`) VALUES
('João da Silva', 'joao.silva@example.com', 'Observação 1', 'Rua A, 123', '11111111111', '1990-01-01', '1111111111'),
('Maria Souza', 'maria.souza@example.com', 'Observação 2', 'Av. B, 456', '22222222222', '1990-02-02', '2222222222'),
('Pedro Almeida', 'pedro.almeida@example.com', 'Observação 3', 'Rua C, 789', '33333333333', '1990-03-03', '3333333333'),
('Ana Santos', 'ana.santos@example.com', 'Observação 4', 'Av. D, 321', '44444444444', '1990-04-04', '4444444444'),
('Fernanda Costa', 'fernanda.costa@example.com', 'Observação 5', 'Rua E, 654', '55555555555', '1990-05-05', '5555555555'),
('Rafael Oliveira', 'rafael.oliveira@example.com', 'Observação 6', 'Av. F, 987', '66666666666', '1990-06-06', '6666666666'),
('Camila Pereira', 'camila.pereira@example.com', 'Observação 7', 'Rua G, 567', '77777777777', '1990-07-07', '7777777777'),
('Lucas Rodrigues', 'lucas.rodrigues@example.com', 'Observação 8', 'Av. H, 432', '88888888888', '1990-08-08', '8888888888'),
('Mariana Ferreira', 'mariana.ferreira@example.com', 'Observação 9', 'Rua I, 789', '99999999999', '1990-09-09', '9999999999'),
('Gabriel Carvalho', 'gabriel.carvalho@example.com', 'Observação 10', 'Av. J, 987', '10101010101', '1990-10-10', '1010101010');
```

### Crie um arquivo .env na raiz do projeto, copiando o conteúdo do arquivo .env.example Atenção os dados do .env que você vai criar na sua máquina podem ser diferentes dos dados originais do .env.example, contanto que sejam condizentes com os dados do seu banco.
## [ATENÇÃO], sem o .env o laravel não gera a chave (key).
## Gere uma nova chave criptografica
```bash
php artisan key:generate
```
### Este comando não vai influenciar diretamente no processo de execução de uma aplicação simples como essa, mas mesmo assim é importante mantermos a segurança.
## Iniciar o Projeto
```bash
php artisan serve
```

