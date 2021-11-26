<?php
/**
 * Oferece metodos estaticos que facilitam criar, manipular e validar Json Web Tokens.
 * Author: Elias Lazcano Castro Neto
 * Version: 2021-06-17
 */

namespace App\Helpers;

class JwtHelper
{
  /* === Propriedades === */

  private $token;
  private $segredo;

  /* === Metodos do Objeto === */

  /**
   * Construtor do Token.
   * @param string $segredo Uma chave, usada para criptografar o token. Se não for passada, será gerada aleatoriamente.
   * @param array $dados Array de dados que o token carregará, podendo ser consultado abertamente.
   */
  public function __construct($segredo = null, $dados = array())
  {
    if (!$segredo) $segredo = md5(uniqid(mt_rand().mt_rand(), true), false);

    $header = array(
      'alg' => 'HS256', //algoritmo de criptografia
      'typ' => 'JWT'    //tipo de token
    );
    $header = json_encode($header);         //converte em string JSON
    $header = base64_encode($header);       //codifica em texto BASE64

    $dados = json_encode((object) $dados);  //converte em string JSON
    $dados = base64_encode($dados);         //codifica em texto BASE64

    $signature = hash_hmac('sha256', "$header.$dados", $segredo, true);
    $signature = base64_encode($signature);

    $this->token = "$header.$dados.$signature";
    $this->segredo = $segredo;
  }

  public function __toString()
  {
    return $this->getToken();
  }

  /**
   * Obtem o token padronizado em string JWT
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }

  /**
   * Obtem a chave secreta capaz de autenticar o token
   * @return string
   */
  public function getSegredo()
  {
    return $this->segredo;
  }

  /**
   * Obtem um array com os dados contidos no Token
   * @return array
   */
  public function getDados()
  {
    $part = explode(".", $this->token);
    $payload = $part[1];
    $payload = base64_decode($payload);
    $payload = json_decode($payload);
    return $payload;
  }

  /**
   * Indica se o segredo é autentico deste token
   * @param string $segredo A chave que foi utilizada na criação do token
   * @return bool
   */
  public function validarSegredo($segredo)
  {
    $part = explode(".", $this->token);
    $header = $part[0];
    $payload = $part[1];
    $signature = $part[2];

    $valid = hash_hmac('sha256', "$header.$payload", $segredo, true);
    $valid = base64_encode($valid);

    return $signature === $valid;
  }

  /* === Metodos da Classe === */

  /**
   * @param string|null $segredo Uma chave de sua escolha para futuramente validar a autenticidade do token. Se nao informado, sera gerada aleatoriamente.
   * @param array $dados Dados incorporados ao token. Evite dados sigilosos como senhas.
   * @return array|string Se voce nao fornecer um segredo, o retorno sera um array com os indices 'token' e 'segredo'
   */
  public static function criarToken($segredo = null, $dados = array())
  {
    $key = $segredo ? $segredo : md5(uniqid(mt_rand().mt_rand(), true), false);

    $header = array(
      'alg' => 'HS256', //informa o algoritmo de criptografia
      'typ' => 'JWT'    //informa o tipo de token
    );
    $header = json_encode($header);         //converte em string JSON
    $header = base64_encode($header);       //codifica em texto BASE64

    $payload = json_encode((object) $dados);  //converte em string JSON
    $payload = base64_encode($payload);         //codifica em texto BASE64

    $signature = hash_hmac('sha256', "$header.$payload", $key, true); //gera a assinatura publica
    $signature = base64_encode($signature); //codifica em texto BASE64

    $token = "$header.$payload.$signature";
    return $segredo ? $token : array("segredo" => $key, "token" => $token);
  }

  /**
   * Valida um token a partir de uma string de segredo, caso ela coincida com a utilizada na sua criação
   * @param string $token O token em string JWT
   * @param string $segredo A string que foi utilizada na criação do token para criptografar
   * @return bool
   */
  public static function validarToken($token, $segredo)
  {
    $part = explode(".", $token);
    $header = $part[0];
    $payload = $part[1];
    $signature = $part[2];

    $valid = hash_hmac('sha256', "$header.$payload", $segredo, true);
    $valid = base64_encode($valid);

    return $signature === $valid;
  }

  /**
   * Obtem os dados de um Token
   * @param string $token O token em string JWT
   * @param bool $associativo Retornará um array associativo ao invés de objeto.
   * @return object|array
   */
  public static function obterDados($token, $associativo = false)
  {
    $part = explode(".", $token);
    $payload = $part[1];
    $payload = base64_decode($payload);
    $payload = json_decode($payload, $associativo);
    return $payload;
  }
}
