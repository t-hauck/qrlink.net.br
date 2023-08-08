<?php
/*
=> URLS E MÉTODOS HTTP UTILIZADOS
    - Método HTTP: POST
        [1] /link/password/78ad6c
            Redirecionamento para o link curto a partir da página que solicita SENHA para o acesso, para links que possuem senha
        [2] /link/getStats
            Visualização de Estatísticas; obter dados sobre qualquer link salvo 
        [3] /link/getUserStats
            Visualização de Estatísticas; obter dados sobre os links salvos pelo usuário, e armazenados no localstorage
        [4] /link/save
            Cadastrar; salvar um novo link no sistema
    - Método HTTP: DELETE
        [1] /delete/78ad6c
            Exclusão de dados; apagar um link do sistema
            
- Método HTTP: GET
        ?status

        /
            Página Principal
        /sobre
            Sobre o Sistema
        /78ad6c
            Redirecionamento para um link curto
        /links
            Página de estatísticas para o usuário
        /admin
            Págins Adiministrativa
        /noscript
*/





// Configurações de cookies criados pelo PHP
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_name("_qrlink"); // Mudar nome padrão do cookie "PHPSESSID" que não foi chamado, mas é criado automaticamente
session_start(); // Iniciando seção no arquivo de rotas, o primeiro arquivo do site que o Apache irá solicitar


require_once "LinkController.php";
require_once "view/info.php";

$controller      = new LinkController();
$controller_ADM  = new LinkAdmin();

$method = $_SERVER["REQUEST_METHOD"];
$URL_query = check_URLPath();



// Gera um novo token CSRF caso não exista na sessão
if (!isset($_SESSION['submitToken'])) $_SESSION['submitToken'] = bin2hex(random_bytes(50));

if ($method == "GET") {

    if ( isset($_GET["status"]) && $_GET["status"] != "") { // ?status= é enviado pelo Apache com .htaccess
        HTTP_HEADERS($_GET["status"]);
    }

    if ( empty($URL_query[1]) == FALSE ) {
        if ( $URL_query[1] === "sobre" ) { // /sobre == página com conteúdo sobre o sistema
            require_once 'view/sobre.php';
            exit;
        }
        else if ( $URL_query[1] === "links" ) { // /links == estatísticas, informações dos links para o usuário
            require_once 'view/stats_userLinks.php';
            exit;
        }
        else if ( $URL_query[1] === "admin" ) { // /admin == interface administrativa
            require_once 'admin.php';
            exit;
        }
        else if ( $URL_query[1] === "noscript" ){
            HTTP_HEADERS("no-Cache");
            $FQDN = $_SERVER["HTTP_HOST"];
            echo "<p>O site <b>$FQDN</b> precisa de um navegador atualizado e com suporte a JavaScript para funcionar corretamente.<br> Seu navegador atual não oferece suporte a esta tecnologia.</p><p> Utilize os navegadores <a href='https://firefox.com/'>Mozilla Firefox</a> e <a href='https://chrome.com/'>Google Chrome</a> em suas versões mais recentes. <br> Leia sobre como habilitar o JavaScript em seu navegador no site <a href='https://www.enablejavascript.io' target='_blank'>enablejavascript.io</a> ou em <a href='https://www.whatismybrowser.com/pt/detect/is-javascript-enabled' target='_blank'>whatismybrowser.com</a> </p><p>Se o seu navegador estava apenas bloqueando o uso de scripts, <a href='/' class='link'>clique aqui</a> para voltar para a página principal.</p>";
            exit;
        }
        else { // Redirecionamento de Links e página de Erro 404
            // if ( count($URL_query) == 3 && empty($URL_query[2]) ){
            //     HTTP_HEADERS("no-Cache");
            //     $controller->redirectLink("GET", $URL_query[1], "");
            //     exit;
            // }

            if ( count($URL_query) >= 3) {
                HTTP_HEADERS(404);
                HTTP_HEADERS("no-Cache");
                require_once 'view/http_404.php';
                exit;
            }

            // redirecionamento
            $controller->redirectLink("GET", $URL_query[1], "");
            if ($_SESSION['DB_URLNotFound'] == TRUE) { // isset ?
                HTTP_HEADERS(404);
                HTTP_HEADERS("no-Cache");
                require_once 'view/http_404.php';
                unset( $_SESSION["DB_URLNotFound"] );
            }
            exit;
        } // else
    } // $URL_query[1]

    require_once 'view/index.php';
    exit;

} else if ($method == "POST") {
    $POST_Json = json_decode(file_get_contents("php://input"), true); // Requisição em JSON
    $headers = getallheaders();         // Headers HTTP da requisição

    if (isset($headers["Csrf-Token"])) $tk_CSRF = $headers["Csrf-Token"];
    if (isset($headers["CSRF-Token"])) $tk_CSRF = $headers["CSRF-Token"];

    // Valida o token CSRF presente na requisição
    if ( isset($tk_CSRF) && hash_equals($tk_CSRF, $_SESSION['submitToken']) ){

        if ( empty($URL_query[1]) == FALSE ) {
            if ($URL_query[1] == "checkPassword" && empty($URL_query[2]) == FALSE ) { // /checkPassword/78ad6c
                $controller->redirectLink("POST", $URL_query[2], $POST_Json["linkPasswd"] );
                exit;
            }
            else if ($URL_query[1] == "getStats") { // /link/getStats
                $controller->get_Stats($POST_Json["linkCode"]);
                exit;
            }
            else if ($URL_query[1] == "getUserStats") { // /link/getUserStats
                $controller->get_userStats($POST_Json["short_code"]);
                exit;
            }
            else if ($URL_query[1] == "save") { // /link/save
                $controller->Link($POST_Json["linkURL"], $POST_Json["linkPasswd"]);
                exit;
            }
            else if ($URL_query[1] == "delete") {
                // apagar TODOS os resultados da pesquisa == NÃO USADO por problemas de visibilidade do ícone de lixeira no HTML
                // if ( isset($POST_Json["search"]) ){ $controller_ADM->apagarItem_pesquisa($POST_Json["search"]); exit; }
                /////
                // if ( empty($URL_query[2]) == FALSE && $URL_query[2] === "links" ){
                //         $controller_ADM->apagarLINKS(); // APAGAR TUDO
                //         exit;
                // }
                $controller_ADM->apagarItem($POST_Json); // POST_Json == Array
                exit;
            } else { HTTP_HEADERS(405); }
        } else { HTTP_HEADERS(405); } // $URL_query[1]
        
    } else { HTTP_HEADERS(405); } // token CSRF
} else { HTTP_HEADERS(405); }     // method POST
// print_r(error_get_last());

function HTTP_HEADERS($header) {
    if ($header == "no-Cache") { // NÃO ARMAZENAR NADA EM CACHE
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
    }

    // Verificação do Status HTTP
    if (     $header == 400) { // 400 Bad Request
        header("HTTP/1.1 400 Bad Request");
        require_once 'view/index.php';
    }
    else if ($header == 404) { // 404 Not Found
        header("HTTP/1.1 404 Not Found");
    }
    else if ($header == 405) { // 405 Method Not Allowed
        header("HTTP/1.1 405 Method Not Allowed");
    }
    else if ($header == 408) { // 408 Request Timeout
        header("HTTP/1.1 408 Request Timeout");
    }
    else if ($header == 429) { // 429 Too Many Requests
        header("HTTP/1.1 429 Too Many Requests");
    }
}
