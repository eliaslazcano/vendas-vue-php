<?php

namespace App\Helpers;

use App\Database\DbApp;
use App\Config;
use PDO;

class AuthHelper
{
  const SOMENTE_ULTIMO_LOGIN = false;

  /**
   * Registra uma sessão de login no banco de dados, obtem o token JWT.
   * @param int $usuario_id ID da conta de usuario.
   * @param PDO|null $conn Conexão PDO. Se não fornecer, uma nova será criada.
   * @return string Token JWT
   */
  public static function registrarSessao(int $usuario_id, PDO $conn = null): string
  {
    $db = new DbApp($conn);
    $usuario = $db->queryPrimeiraLinha('SELECT nome, senha FROM usuarios WHERE id = :id', [':id' => $usuario_id]);
    if (!$usuario) HttpHelper::erroJson(500, 'Usuário não encontrado.');
    $login_datetime = date('Y-m-d H:i:s');

    $payload = array(
      'id' => 'id da sessao',
      'usuario' => array(
        'id' => $usuario_id,
        'nome' => $usuario['nome']
      ),
      'criado_em' => $login_datetime,
      'validade' => date('Y-m-d H:i:s', time() + (Config::TEMPO_LOGIN * 60)),
      'ip' => HttpHelper::obterIp(),
    );
    $chave = md5(uniqid(mt_rand() . mt_rand(), true), false);
    $token = new JwtHelper($chave, $payload);

    $db->insert('INSERT INTO sessoes (usuario, chave, criado_em) VALUES (:usuario, :chave, :criado_em)', [':usuario' => $usuario_id, ':chave' => $chave, ':criado_em' => $login_datetime]);
    if (session_id()) $_SESSION['chave'] = $chave;
    return $token->getToken();
  }

  /**
   * Através de um token JWT, busca validar a sessão do usuário no sistema. Se nenhum token for passado, será buscado no Header "Authorization"
   * @param PDO|null $conn Conexão PDO. Se não fornecer, uma nova sera usada.
   * @param string|null $token string do token JWT da sessao atual. Se não fornecer será buscada no header Authorization.
   * @return array Retorna o payload do token em forma de array associativo.
   */
  public static function autenticarSessao(PDO $conn = null, string $token = null)
  {
    $token = $token ?: HttpHelper::obterCabecalho('Authorization');
    if (!$token) HttpHelper::erroJson(410, 'O token de segurança não está na sua solicitação', 1);
    $payload = JwtHelper::obterDados($token, true);

    if (session_id() && isset($_SESSION['segredo'])) $chave = $_SESSION['segredo'];
    else {
      $db = new DbApp($conn);
      $sessao = $db->queryPrimeiraLinha('SELECT chave FROM sessoes WHERE usuario = :usuario AND criado_em = :criado_em LIMIT 1', array(':usuario' => $payload['usuario']['id'], ':criado_em' => $payload['criado_em']));
      $chave = $sessao['chave'];
    }

    if (!$chave) HttpHelper::erroJson(410, 'O sistema não reconheceu seu login de acesso', 4);
    if (!JwtHelper::validarToken($token, $chave)) HttpHelper::erroJson(410, 'Acesso negado', 5);
    if ($payload['validade'] < date('Y-m-d H:i:s')) HttpHelper::erroJson(410, 'O tempo da sua sessão expirou', 6);

    //Valida se é o último Token emitido para este usuário
    if (self::SOMENTE_ULTIMO_LOGIN) {
      $db = new DbApp($conn);
      $sessao = $db->queryPrimeiraLinha('SELECT criado_em FROM sessoes WHERE usuario = :usuario ORDER BY id DESC LIMIT 1', array(':usuario' => $payload['usuario']['id']));
      if ($sessao['criado_em'] != $payload['criado_em']) HttpHelper::erroJson(410, 'Sua conta possui um login mais recente de outro local', 7);
    }

    return JwtHelper::obterDados($token, true);
  }
}
