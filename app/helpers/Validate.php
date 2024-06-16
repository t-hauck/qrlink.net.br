<?php

$folder = $_SERVER["DOCUMENT_ROOT"] . "/app/helpers/";
require_once $folder . "Http.php";


function validate_url($url) {
  // return filter_var($url, FILTER_SANITIZE_URL) && filter_var($url, FILTER_VALIDATE_URL);
  // filter_var($url, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ) {
  return (parse_url($url, PHP_URL_SCHEME) && parse_url($url, PHP_URL_HOST)); // if (filter_var($url, FILTER_VALIDATE_URL)) {
}

function validate_code($code) {
  // Validate the code to allow only alphanumeric characters
  return preg_match('/^[a-zA-Z0-9]+$/', $code);
}

function validate_password($passwd) {
  return !empty($passwd) && strlen($passwd) > 0 && strlen(trim($passwd)) > 0;
}


function token_create(){ // Gera um novo token CSRF caso não exista na sessão
  if (Http::method() === "get") {
    if (!isset($_SESSION['submitToken']))
      $_SESSION['submitToken'] = bin2hex(random_bytes(50));
  }
}
function token_validate(){ // Verifica se token existe e corresponde ao gerado
  if (Http::method() === "get") { // não validar token no método GET
    return;
  }

  $headers = getallheaders();         // Headers HTTP da requisição
  if (isset($headers["Csrf-Token"])) $token = $headers["Csrf-Token"];
  if (isset($headers["CSRF-Token"])) $token = $headers["CSRF-Token"];

	if (!isset($token) && !hash_equals($token, $_SESSION['submitToken'])) {
    Http::status(401); // 400
    throw new Exception("CSRF token not found");
  }

  if (!hash_equals($_SESSION['submitToken'], $token) ){ // $_POST
    Http::status(401);
    throw new \Exception("CSRF token mismatch");
  }
}
