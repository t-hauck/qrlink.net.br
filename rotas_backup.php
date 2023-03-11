<?php
///// https://nomadphp.com/blog/64/creating-a-url-shortener-application-in-php-mysql
// Configurações de cookies criados pelo PHP
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', "strict");

session_name("_qrlink"); // Mudar nome padrão do cookie "PHPSESSID" que não foi chamado, mas é criado automaticamente
session_start(); // Iniciando seção no arquivo de rotas, o primeiro arquivo do site que o Apache irá solicitar


require_once "LinkController.php";

$method = $_SERVER["REQUEST_METHOD"];
$route  = $_SERVER["REQUEST_URI"];



if ($method == "POST") {
    $post_token = isset($_POST['submitToken']);
    $session_token = isset($_SESSION["submitToken"]);
  
    if(!empty(htmlentities($post_token) )) {
         if(htmlentities($post_token) == $session_token ) { // a requisição POST veio do próprio site e está autorizada
            $controller = new LinkController();
            $controller->Link($_REQUEST); 
            exit();
        } else { // se 'token' existe mas NÃO é igual ao armazenado em seção
            HTTPStatus(405);
        }
    } else { // se 'token' NÃO existe na requisição POST
        HTTPStatus(405);
    }
} else if ($method == "GET") {
    $URL_query = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    if ( isset($_GET["status"]) && $_GET['status'] != "") { /// ?status= é enviado pelo Apache com .htaccess
        HTTPStatus($_GET["status"]); 
        exit();  
    }
    if ( empty($URL_query[1]) == FALSE ) {
        if ( $URL_query[1] == "qrlink" && isset($URL_query[2]) == "admin" ) { // https://qrlink.hauck.net.br/qrlink/admin 
            // interface administrativa => EM CONSTRUÇÃO
            require_once 'admin.php';
            exit();
        } else {       
            $controller = new LinkController();
            $controller->redirectLink($URL_query[1]);
            exit();
        }
    }

    require_once 'view/index.php';
    exit();
} else {
    HTTPStatus(405);
}

function HTTPStatus($status) { //// Verificação do Status HTTP
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    if ($status == "400") { // 400 Bad Request
        echo "400 Bad Request";
        header("HTTP/1.1 400 Bad Request");
        exit();
    }
    else if ($status == "404") { // 404 Not Found
        require_once 'view/index.php';
        header("HTTP/1.1 404 Not Found");
        exit();
    }
    else if ($status == "405") { // 405 Method Not Allowed
        echo "405 Method Not Allowed";
        header("HTTP/1.1 405 Method Not Allowed");
        exit();
    }
    else if ($status == "408") { // 408 Request Timeout
        echo "408 Request Timeout";
        header("HTTP/1.1 408 Request Timeout");
        exit();
    }
    else if ($status == "429") { // 429 Too Many Requests
        echo "429 Too Many Requests";
        header("HTTP/1.1 429 Too Many Requests");
        exit();
    }
} //// print_r(error_get_last());
