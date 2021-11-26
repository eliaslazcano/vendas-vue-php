<?php
/**
 * Entry Point
 * Este arquivo é executado globalmente antes de qualquer serviço.
 */

ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/errors.log');
ini_set('memory_limit','512M');
error_reporting(E_ALL);

//Autoload das classes
spl_autoload_register(function ($className) {
  $barra = strripos($className, '\\');
  if ($barra) $className = substr($className, $barra + 1);
  if (file_exists(__DIR__ . '/'.$className.'.php')) require_once(__DIR__ . '/'.$className.'.php');
  if (file_exists(__DIR__ . '/database/'.$className.'.php')) require_once(__DIR__ . '/database/'.$className.'.php');
  if (file_exists(__DIR__ . '/helper/'.$className.'.php')) require_once(__DIR__ . '/helper/'.$className.'.php');
  if (file_exists(__DIR__ . '/models/'.$className.'.php')) require_once(__DIR__ . '/models/'.$className.'.php');
  if (file_exists(__DIR__ . '/dao/'.$className.'.php')) require_once(__DIR__ . '/dao/'.$className.'.php');
  if (file_exists(__DIR__ . '/http/'.$className.'.php')) require_once(__DIR__ . '/http/'.$className.'.php');
});

use App\Helpers\HttpHelper;

session_start();

//Desligar temporariamente a aplicacao? Descomente abaixo:
//HttpHelper::validarMetodos(['GET', 'POST', 'PUT', 'DELETE', 'PATCH']);
//HttpHelper::erroJson(400, 'Sistema em manutenção, tente mais tarde', 1, 'Backend desativado');

$pathInfo = isset($_SERVER['PATH_INFO']);
$origPathInfo = isset($_SERVER['ORIG_PATH_INFO']);
if ($pathInfo) $caminho = $_SERVER['PATH_INFO'];
elseif ($origPathInfo) $caminho = $_SERVER['ORIG_PATH_INFO'];
else HttpHelper::erroJson(404, 'Página não encontrada');

//Remove a barra inicial
if (substr($caminho, 0, 1) === '/') $caminho = substr($caminho, 1);
// Se houver URL definida
if ($caminho) {
  if (file_exists(__DIR__.'/services/' . $caminho . '.php')) {
    require __DIR__.'/services/' . $caminho . '.php';
  }
  elseif (file_exists(__DIR__.'/services/' . $caminho . '/index.php')) {
    require __DIR__.'/services/' . $caminho . '/index.php' ;
  }
  else HttpHelper::erroJson(404, 'Página não encontrada');
}
// Quando não há URl definida
else HttpHelper::erroJson(404, 'Página não encontrada');
