<?php
/**
 * POST => Adiciona um produto em uma venda. Retorna o ID deste registro.
 * PUT => Atualiza/Altera os dados do produto que está contido na venda.
 * DELETE => Remove um produto de uma venda.
 */

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarMetodos(['POST','PUT','DELETE']);
$db = new DbApp();
$payload = AuthHelper::autenticarSessao($db->getConexao());

if (HttpHelper::isPost()) {
  $venda = HttpHelper::validarParametro('venda');
  $produto = HttpHelper::validarParametro('produto');
  $quantidade = HttpHelper::validarParametro('quantidade');
  $preco_unitario = HttpHelper::validarParametro('preco_unitario');
  $sql = 'INSERT INTO vendas_itens (venda, produto, quantidade, preco_unitario, valor) VALUES (:venda, :produto, :quantidade, :preco_unitario, :valor)';
  $id = $db->insert($sql, [':venda' => $venda, ':produto' => $produto, ':quantidade' => $quantidade, ':preco_unitario' => $preco_unitario, ':valor' => $preco_unitario * $quantidade]);
  HttpHelper::emitirJson((int) $id);
} elseif (HttpHelper::isPut()) {
  $id = HttpHelper::validarParametro('id');
  $quantidade = HttpHelper::validarParametro('quantidade');
  $preco_unitario = HttpHelper::validarParametro('preco_unitario');
  $sql = 'UPDATE vendas_itens SET quantidade = :quantidade, preco_unitario = :preco_unitario WHERE id = :id';
  $afetados = $db->update($sql, [':quantidade' => $quantidade, ':preco_unitario' => $preco_unitario, ':id' => $id]);
  HttpHelper::emitirJson($afetados && $afetados > 0);
} elseif (HttpHelper::isDelete()) {
  $id = HttpHelper::validarParametro('id');
  $afetados = $db->update('DELETE FROM vendas_itens WHERE id = :id', [':id' => $id]);
  HttpHelper::emitirJson($afetados && $afetados > 0);
}
