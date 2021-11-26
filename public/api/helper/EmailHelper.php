<?php

namespace App\Helpers;

use App\Config;
use App\Database\Atdonline;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../lib/vendor/autoload.php';

class EmailHelper
{
  private $assunto;
  private $mensagem;
  private $mailer;
  private $conn;
  public $error;

  /**
   * EmailHelper constructor.
   * @param string $assunto
   * @param string $mensagem
   * @param PDO $conn
   */
  public function __construct($assunto, $mensagem, $conn = null)
  {
    $this->assunto        = $assunto;
    $this->mensagem       = $mensagem;
    $this->mailer         = new PHPMailer(true);
    $this->conn = $conn ?: Atdonline::obterConexao();
  }

  public function addDestinatario($endereco, $nome = '')
  {
    try {
      $this->mailer->addAddress($endereco, $nome);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  public function addCC($endereco, $nome = '')
  {
    try {
      $this->mailer->addCC($endereco, $nome);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }
  public function addCCO($endereco, $nome = '')
  {
    try {
      $this->mailer->addBCC($endereco, $nome);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function addAnexo($path, $nome = '')
  {
    try {
      $this->mailer->addAttachment($path, $nome);
      return true;
    } catch (Exception $e) {
      return false;
    }
  }

  public function enviar($naoEmitirErro = false) {
    $statement = Atdonline::executedStatement('SELECT * FROM configuracao ORDER BY id DESC LIMIT 1', [], $this->conn);
    $configuracao = $statement->fetch(PDO::FETCH_ASSOC);
    if (!$configuracao) HttpHelper::erroJson(500, 'Ausência de configuração no sistema');
    $mail = $this->mailer;

    try {
      //Server settings
      $mail->SMTPDebug = $configuracao['email_debugar'] ? SMTP::DEBUG_CONNECTION : SMTP::DEBUG_OFF;
      $mail->isSMTP();
      $mail->Host       = $configuracao['email_smtp_host'];
      $mail->SMTPAuth   = true;
      $mail->Username   = $configuracao['email_smtp_login'];
      $mail->Password   = $configuracao['email_smtp_senha'];
      $mail->SMTPSecure = $configuracao['email_smtp_tls'] ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
      $mail->Port       = intval($configuracao['email_smtp_porta']);

      //Recipients
      $mail->setFrom($configuracao['email_smtp_login'], $configuracao['email_remetente_nome']);
      $mail->addReplyTo($configuracao['email_responder_para'], $configuracao['email_responder_nome']);

      // Content
      $mail->isHTML(true);
      $mail->Subject = $this->assunto;
      $mail->Body    = $this->mensagem;
      $mail->CharSet = PHPMailer::CHARSET_UTF8;

      $mail->send();
      return true;
    } catch (Exception $e) {
      $this->error = $mail->ErrorInfo . '. ' . $e->getMessage();
      if (!$naoEmitirErro) HttpHelper::erroJson(500, "Não foi possível enviar o email", 0, array($mail->ErrorInfo, $e));
      else {
        Atdonline::reportarErro('Falha ao tentar enviar um email. ErrorInfo: '.$mail->ErrorInfo.'. Exception(getMessage): '. $e->getMessage(). '. Exception(errorMessage): '. $e->errorMessage());
        return false;
      }
    }
  }
}
