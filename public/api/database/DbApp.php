<?php

namespace App\Database;

use App\Helpers\HttpHelper;

class DbApp extends DbModel
{
  protected $host = 'eliasneto.ddns.net';
  protected $usuario = 'root';
  protected $senha = '501zinh0';
  protected $base_de_dados = 'barjk';

  protected function aoFalhar($mensagem) {
    HttpHelper::erroJson(507, "Falha na base de dados da aplicação [$mensagem", 1, $mensagem);
  }
}