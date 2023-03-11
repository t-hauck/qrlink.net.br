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
// Para obter uma requisição em JSON:
//// $REQUEST_delete = json_decode(file_get_contents("php://input"), true);

    if ( isset($_POST['submitToken']) && isset($_SESSION['submitToken']) && check_token($_POST['submitToken'], $_SESSION["submitToken"]) ){

        if ( empty($URL_query[1]) == FALSE && $URL_query[1] == "link" ) {

            if ( empty($URL_query[2]) == FALSE  ) {
                if ($URL_query[2] == "save") { // /link/save 
                                            ///// Cadastro de novo link
                    $controller = new LinkController();
                    $controller->Link($_REQUEST);
                    exit();
                }
                else if ($URL_query[2] == "delete") {   // /link/delete 
                                                ///// Exclusão de dados apenas na Interface Administrativa
                    $controller = new LinkAdmin();

                    if ( isset($_REQUEST["short_code"]) ) {
                                // APAGAR TUDO
                        if ( $_REQUEST["short_code"] === "links" ){ $controller->apagarLINKS(); exit; }
                        else {  // apagar APENAS UM, pelo botão na linha do link da tabela
                            $controller->apagarItem($_REQUEST); exit;
                        }
                    }
                                // apagar TODOS os resultados da pesquisa
                    if ( isset($_REQUEST["search"]) ) $controller->apagarItem_pesquisa($_REQUEST); exit;

                    HTTPStatus(405);
                } else { HTTPStatus(405); } // $URL_query[2] == última URL usada em POST

            } else { HTTPStatus(405); } // $URL_query[2]
        } else { HTTPStatus(405); }     // $URL_query[1]
    } else { HTTPStatus(405); }         // token
    
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
        }
        if ($URL_query[1] == "noscript"){
            $FQDN = $_SERVER["HTTP_HOST"];
            echo "<p>O site <b>$FQDN</b> precisa de um navegador atualizado e com suporte a JavaScript para funcionar corretamente.<br> Seu navegador atual não oferece suporte a esta tecnologia.</p><p> Utilize os navegadores <a href='https://firefox.com/'>Mozilla Firefox</a> e <a href='https://chrome.com/'>Google Chrome</a> em suas versões mais recentes. <br> Leia sobre como habilitar o JavaScript em seu navegador no site <a href='https://www.enablejavascript.io' target='_blank'>enablejavascript.io</a></p><p>Se o seu navegador estava apenas bloqueando o uso de scripts, <a href='/' class='link'>clique aqui</a> para voltar para a página principal.</p>";
            exit;
        } else {       
            $controller = new LinkController();
            $controller->redirectLink($URL_query[1]);
            exit();
        }
    }

    require_once 'view/index.php';
    exit();
} else { HTTPStatus(405); }

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
