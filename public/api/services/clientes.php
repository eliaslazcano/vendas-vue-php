<?php

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarMetodos(['GET','POST']);
$db = new DbApp();
$payload = AuthHelper::autenticarSessao($db->getConexao());

if (HttpHelper::isGet()) {
  $clientes = $db->query('SELECT id, nome FROM clientes', [], ['id']);
  HttpHelper::emitirJson($clientes);
} elseif (HttpHelper::isPost()) {
  $nome = HttpHelper::validarParametro('nome');
  $cpf = HttpHelper::obterParametro('cpf');
  $db->insert('INSERT INTO clientes (nome, cpf, criado_por) VALUES (:nome, :cpf, :criado_por)', [':nome' => mb_strtoupper($nome, 'UTF-8'), ':cpf' => $cpf, ':criado_por' => $payload['usuario']['id']]);
}
