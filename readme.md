## _Wppconnect PHP Bot_

Bot multi-números para ser usado no webhook do Wppconnect Server

## Requisitos

* PHP 7.4 ou superior com driver do MongoDB.
* MongoDB

## Configuração

As configurações de ambiente ficam ou na classe do bot quando forem únicas, ou no arquivo .env quando forem globais. 

Duplique .env.example e renomeie para .env. 
## _Wppconnect PHP Bot_

Bot multi-números para ser usado no webhook do Wppconnect Server

## Requisitos

* PHP 7.4 ou superior com driver do MongoDB.
* MongoDB

## Configuração

As configurações de ambiente ficam ou na classe do bot quando forem únicas, ou no arquivo .env quando forem globais. 

Duplique .env.example e renomeie para .env. 

## INSTALACAÇÃO LINUX UBUNTU


---------------diretorio html linux -----------------------
$ cd /var/www/html/
---------------  baixando arquivos do git -----------------------

$ git clone https://github.com/ghsgabriel/wppconnect-php-bot.git

---------------Renomiando a pasta -----------------------
$ mv wppconnect-php-bot/ agua -- dessa forma meu diretorio ficaria /var/www/html/agua

---------------Validando versao do php -----------------------
$ php -v  
PHP 7.4.13 (cli) (built: Nov 28 2020 06:24:43) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.13, Copyright (c), by Zend Technologies
root@SRVWEBJR:/var/www/html# php -v
PHP 7.4.13 (cli) (built: Nov 28 2020 06:24:43) ( NTS )
Copyright (c) The PHP Group
Zend Engine v3.4.0, Copyright (c) Zend Technologies
    with Zend OPcache v7.4.13, Copyright (c), by Zend Technologies

---------------instalando driver mongo db  -----------------------

$ sudo apt install php-pear
$ sudo apt-get install -y php-dev pkg-config
$ sudo pecl install mongodb
adicionar  extension=mongodb.so no  php.ini
$ sudo nano /etc/php/7.2/apache2/php.ini 


Adicionar a linha extension=mongo.so no php.ini

---------------Criar e configurar conta mongodb  -----------------------

Entrar no site https://www.mongodb.com/
Cadastrar no site

    1 - ir en Security => Database Access y criar a um usuario e senha 
    2 - voltar a Data Storage = Cluster esperar que ative o botao de connect.
    3 - Clicar en Connect seleccionar a opcao Connect Your application
        3.1 Select your Driver and Version 
        3.2 Driver = PHP
        3.3 Version PHPLIB 1.3 + mongodb-1.4 or later
    4 - copiar a string de conexao algo como "mongodb+srv://user:pass@server.mongodb.net/myFirstDatabase?retryWrites=true&w=majority"

    5 - fazer copia do arquivo .env.exemple para .env

    $ cp .env.exemple .env

    $ sudo nano .env 
        aqui devemos editar os dados da conexão
        MONGODB_STRING=mongodb+srv://user:pass@server.mongodb.net/myFirstDatabase?retryWrites=true&w=majority

    Criando Banco de dados e colletion mongodb
    1 - entrar no site https://www.mongodb.com/try/download/compass  e baixar e instalar a versão compativel com seu SO
    2 - depois de instalado clicar en conectar e colcoar o a string de conexao para conectar no banco 
    3 - Seguir os passos da imagem aqui https://prnt.sc/192x7u3



## Uso
Cada classe dentro da pasta src/session é um bot, um número.
Todos extendem a classe Session.php
A nomeclatura para cada classe de bot precisa ser N + BOTNUMBER
O sistema automáticamente identifica quem está recebendo mensagem, quem está enviando e cria um state para essa conversa no MongoDB, no fluxo você pode adicionar parâmetros sobre essa conversa.

## Uso
Cada classe dentro da pasta src/session é um bot, um número.
Todos extendem a classe Session.php
A nomeclatura para cada classe de bot precisa ser N + BOTNUMBER
O sistema automáticamente identifica quem está recebendo mensagem, quem está enviando e cria um state para essa conversa no MongoDB, no fluxo você pode adicionar parâmetros sobre essa conversa.
