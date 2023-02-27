<?php
// https://pt.stackoverflow.com/questions/9419/quais-s%c3%a3o-os-m%c3%a9todos-de-requisi%c3%a7%c3%a3o-http-e-qual-%c3%a9-a-diferen%c3%a7a-entre-eles?noredirect=1&lq=1
///// https://nomadphp.com/blog/64/creating-a-url-shortener-application-in-php-mysql
// Configurações de cookies criados pelo PHP
ini_set('session.cookie_secure', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', "strict");

session_name("_qrlink"); // Mudar nome padrão do cookie "PHPSESSID" que não foi chamado, mas é criado automaticamente
session_start(); // Iniciando seção no arquivo de rotas, o primeiro arquivo do site que o Apache irá solicitar


require_once "LinkController.php";

$method = $_SERVER["REQUEST_METHOD"];
$URL_query = explode("/", parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));




if ($method == "POST") {
    if ( isset($_POST['submitToken']) && isset($_SESSION['submitToken']) && check_token($_POST['submitToken'], $_SESSION["submitToken"]) ){

        if ( empty($URL_query[1]) == FALSE && $URL_query[1] == "link" ) {

            if ( empty($URL_query[2]) == FALSE  ) {
                if ($URL_query[2] == "save") { // /link/save 
                                            ///// Cadastro de novo link
                    $controller = new LinkController();
                    $controller->Link($_REQUEST);
                    exit();
                }

                if ($URL_query[2] == "delete") {   // /link/delete 
                                                ///// Exclusão de dados apenas na Interface Administrativa
                    // Para obter uma requisição em JSON:
                    // $REQUEST_delete = json_decode(file_get_contents("php://input"), true);
                    $controller = new LinkAdmin();
                    if ( $_REQUEST["short_code"] === "links" ) { $controller->apagarLINKS(); exit; }
                    else { $controller->apagarItem($_REQUEST); exit; }
                }
            }
        } else { HTTPStatus(405); }
    } else {
        HTTPStatus(405);
        exit;
    }
} else if ($method == "GET") {
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache'); // NÃO ARMAZENAR NADA EM CACHE
    header('Expires: 0');

    if ( isset($_GET["status"]) && $_GET['status'] != "") { /// ?status= é enviado pelo Apache com .htaccess
        HTTPStatus($_GET["status"]); 
        exit();  
    }
    if ( !empty($URL_query[1]) ) { // empty($...) == FALSE 
        if ( $URL_query[1] == "qrlink" && !empty($URL_query[2]) && $URL_query[2] == "admin" ) { // /qrlink/admin 
                                                                                                // interface administrativa
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
    if (          $status == "200") { // 200 OK == ErrorDocument /?status=200
        require_once 'view/index.php';
        header("HTTP/1.1 200 OK");
        exit();
    }
    else if (     $status == "400") { // 400 Bad Request
        header("HTTP/1.1 400 Bad Request");
        exit();
    }
    else if ($status == "405") { // 405 Method Not Allowed
        header("HTTP/1.1 405 Method Not Allowed");
        exit();
    }
    else if ($status == "408") { // 408 Request Timeout
        header("HTTP/1.1 408 Request Timeout");
        exit();
    }
    else if ($status == "429") { // 429 Too Many Requests
        header("HTTP/1.1 429 Too Many Requests");
        exit();
    }
} //// print_r(error_get_last());


function check_token($post_token, $session_token) {
    // echo $post_token . "<br>" . $session_token . "<br><br>";

    if(!empty(htmlentities($post_token) )) { // TRUE == 1
        if(htmlentities($post_token) == $session_token ) { // a requisição veio do próprio site e está autorizada
            return TRUE;
        } else { // se 'token' existe mas NÃO é igual ao armazenado em seção
            return FALSE;
        }
    } else { // se 'token' NÃO existe na requisição
        return FALSE;
    }
}
