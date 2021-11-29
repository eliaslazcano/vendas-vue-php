<?php

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarMetodos(['GET','POST']);
$db = new DbApp();
$payload = AuthHelper::autenticarSessao($db->getConexao());

if (HttpHelper::isGet()) {
  $vendas = $db->query('SELECT v.id, v.cliente, c.nome as cliente_nome, v.credito, v.criado_em FROM vendas v LEFT JOIN clientes c on v.cliente = c.id', [], ['id','cliente','credito']);
  HttpHelper::emitirJson($vendas);
} elseif (HttpHelper::isPost()) {
  $cliente = HttpHelper::obterParametro('cliente');
  $id = $db->insert('INSERT INTO vendas (cliente, criado_por) VALUES (:cliente, :usuario)', [':cliente' => $cliente, ':usuario' => $payload['usuario']['id']]);
  HttpHelper::emitirJson($id);
}
