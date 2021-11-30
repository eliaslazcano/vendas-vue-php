<?php

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarGet();
$db = new DbApp();
$payload = AuthHelper::autenticarSessao($db->getConexao());
$id = HttpHelper::validarParametro('id');

$sql = 'SELECT v.id, v.credito, v.cliente, c.nome as cliente_nome, v.criado_em, v.criado_por, u.nome as criado_por_nome FROM vendas v LEFT JOIN clientes c on v.cliente = c.id LEFT JOIN usuarios u on c.criado_por = u.id WHERE v.id = :id';
$venda = $db->queryPrimeiraLinha($sql, [':id' => $id], ['id','credito','cliente','criado_por']);
if (!$venda) HttpHelper::erroJson(400, 'Venda não encontrada no banco de dados');

$sql = 'SELECT i.id, i.produto, p.nome as produto_nome, i.quantidade, i.preco_unitario, i.valor FROM vendas_itens i INNER JOIN produtos p on i.produto = p.id WHERE i.venda = :venda';
$venda['itens'] = $db->query($sql, [':venda' => $id], ['id','produto','quantidade','preco_unitario','valor']);

HttpHelper::emitirJson($venda);
