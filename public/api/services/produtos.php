<?php
/**
 * GET => Fornece a lista de produtos.
 * POST => Cria um produto, ou atualiza caso envie o ID.
 */

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarMetodos(['GET','POST','DELETE']);
$db = new DbApp();
$payload = AuthHelper::autenticarSessao($db->getConexao());

if (HttpHelper::isGet()) {
  $produtos = $db->query('SELECT id, nome, preco, codigo FROM produtos WHERE deletado_em IS NULL', [], ['id','preco']);
  HttpHelper::emitirJson($produtos);
} elseif (HttpHelper::isPost()) {
  $id = HttpHelper::obterParametro('id');
  $nome = HttpHelper::validarParametro('nome');
  $preco = HttpHelper::validarParametro('preco');
  $codigo = HttpHelper::obterParametro('codigo');
  if ($id) {
    $afetados = $db->update('UPDATE produtos SET deletado_em = current_timestamp, deletado_por = :deletado_por WHERE id = :id', [':deletado_por' => $payload['usuario']['id'], ':id' => $id]);
    if (!$afetados) HttpHelper::erroJson(400, 'Nenhum produto foi afetado, talvez ele não exista mais ou você não modificou nenhuma informação.');
    $id = $db->insert('INSERT INTO produtos (nome, preco, codigo, criado_por, original) VALUES (:nome, :preco, :codigo, :criado_por, :original)', [':nome' => $nome, ':preco' => $preco, ':codigo' => $codigo, ':criado_por' => $payload['usuario']['id'], ':original' => $id]);
  }
  else {
    $id = $db->insert('INSERT INTO produtos (nome, preco, codigo, criado_por) VALUES (:nome, :preco, :codigo, :criado_por)', [':nome' => $nome, ':preco' => $preco, ':codigo' => $codigo, ':criado_por' => $payload['usuario']['id']]);
  }
  HttpHelper::emitirJson($id);
} elseif (HttpHelper::isDelete()) {
  $id = HttpHelper::validarParametro('id');
  $afetados = $db->update('UPDATE produtos SET deletado_em = current_timestamp, deletado_por = :deletado_por WHERE id = :id', [':deletado_por' => $payload['usuario']['id'], ':id' => $id]);
  if (!$afetados) HttpHelper::erroJson(400, 'O produto foi encontrado, talvez ele não exista mais.');
}
