<?php

use App\Helpers\HttpHelper;
use App\Helpers\AuthHelper;
use App\Database\DbApp;

HttpHelper::validarPost();
$usuario = HttpHelper::validarParametro('usuario');
$senha = HttpHelper::validarParametro('senha');
$db = new DbApp();
$usuario = $db->queryPrimeiraLinha('SELECT id, senha, desativado FROM usuarios WHERE usuario = :usuario', [':usuario' => $usuario], ['id']);
if (!$usuario) HttpHelper::erroJson(400, 'Nenhum usuário encontrado com este nome');
if ($usuario['desativado']) HttpHelper::erroJson(400, 'Sua conta está desativada desde ' . $usuario['desativado']);
if ($usuario['senha'] !== md5($senha)) HttpHelper::erroJson(400, 'Senha incorreta');
HttpHelper::emitirJson(AuthHelper::registrarSessao($usuario['id'], $db->getConexao()));
