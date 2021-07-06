## _Wppconnect PHP Bot_

Bot multi-números para ser usado no webhook do Wppconnect Server

## Requisitos

* PHP 7.4 ou superior com driver do MongoDB.
* MongoDB

## Configuração

As configurações de ambiente ficam ou na classe do bot quando forem únicas, ou no arquivo .env quando forem globais. 

Duplique .env.example e renomeie para .env. 

## Uso
Cada classe dentro da pasta src/session é um bot, um número.
Todos extendem a classe Session.php
A nomeclatura para cada classe de bot precisa ser N + BOTNUMBER
O sistema automáticamente identifica quem está recebendo mensagem, quem está enviando e cria um state para essa conversa no MongoDB, no fluxo você pode adicionar parâmetros sobre essa conversa.
