<?php
// Configurações de cookies criados pelo PHP
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_name("_qrlink"); // default cookie "PHPSESSID"
session_start();

$folder = $_SERVER["DOCUMENT_ROOT"] . "/app/helpers/";
require_once $folder . "Http.php";
require_once $folder . "Validate.php";



class Router {

    private static function debug(){
        echo "browser_noscript => " . $_SESSION["browser_noscript"] . " <br>browser_noscript_PreviousUrl => " . $_SESSION["browser_noscript_PreviousUrl"];
        echo "<br><br>";
        var_dump($_SESSION);
    }

    private static function routes():array {
        return [
            'get' => [
                '/' => fn() => self::loadView('home.php'),
                '/links' => fn() => self::loadView('link_users.php'),
                '/sobre' => fn() => self::loadView('about.php'),
                '/contato' => fn() => self::loadView('contact.php'),
                '/admin' => fn() => self::loadView('admin_page/dashboard.php'),
                '/serverstats' => fn() => self::load('LinkController', 'get_ServerStats'),
                '_noscript_' => fn() => self::loadView("noscript"),
                '_notfound_' => fn() => self::loadView(["status" => "404"]),
                '/([\w]+)' => fn($code) => self::load('LinkController', 'redirectLink', $code),
            ],
            'post' => [
                '/save' => fn() => self::load('LinkController', 'Link'),
                '/getstats' => fn() => self::load('LinkController', 'get_Stats'),
                '/getuserstats' => fn() => self::load('LinkController', 'get_UserStats'),
                '/link/save' => fn() => self::load('LinkController', 'save'),
                '/checkpassword/([\w]+)' => fn($code) => self::load('LinkController', 'redirectLink', $code),
                '/admin' => fn() => self::load('LinkController', 'showLinks'),
                '/admin/block' => fn() => self::load('LinkController', 'blockLink'),
                '/admin/block/schedule' => fn() => self::load('LinkController', 'blockLink'),
                '/admin/delete' => fn() => self::load('LinkController', 'deleteLinks'),
                '_notfound_' => fn() => self::loadView(["status" => "404-Text"]),
            ],
            'delete' => [
                '/admin/delete/([\w]+)' => fn($code) => self::load('LinkController', 'deleteOneLink', $code),
                '_notfound_' => fn() => self::loadView(["status" => "404-Text"]),
            ],
        ];
    }

    public static function loadView($h) {
        $fileHttp = "http_statusCodes.php";
        $uri = Http::requestURI("path");

        if(is_string($h)) {
            $PAGES = $_SERVER["DOCUMENT_ROOT"] . "/app/view/pages/";
            $file = $PAGES . $h;

            if ($h === "noscript") { // acesso direto a rota /noscript
                $_SESSION["browser_noscript"] = true;           // variável verifcada pelo $fileHttp
                $_SESSION["browser_noscript_ShowPage"] = true;  // após reload, exibir ou não a página?

                if (isset($_SESSION["browser_noscript_PreviousUrl"])) {
                    header("Location: " . $_SESSION["browser_noscript_PreviousUrl"]);
                    exit;
                } else { // primeiro acesso
                    header("Location: /");
                    exit;
                }
            }
            else {
                if (isset($_SESSION["browser_noscript_ShowPage"]) && $_SESSION["browser_noscript_ShowPage"] === TRUE) {
                    unset($_SESSION["browser_noscript_ShowPage"]);
                    self::loadView($fileHttp); // exibir HTML noScript no segundo acesso após reload
                    exit;
                }

                $_SESSION["browser_noscript_PreviousUrl"] = $uri;

                if (!empty($h) && file_exists($file)) {
                    require_once($file);
                    exit;
                } else {
                    Http::status(404);
                    self::loadView($fileHttp); // self::loadView == file_exists ?
                    exit;
                }
            }
        }
        else if(is_array($h)) { // self::loadView(["status" => 404]);
            if (array_key_exists("status", $h) === FALSE) {
                throw new InvalidArgumentException("Chave 'status' não encontrada no array recebido em Router::loadView");
            }

            if ($h["status"] === '404-Text') {
                Http::status(404);
                echo "404 Not Found";
                exit;  // throw new Exception("404 Not Found");
            } else {
                Http::status($h["status"]);
                self::loadView($fileHttp);
                exit;
            }
        }else {
            throw new InvalidArgumentException("Parâmetro inválido passado para Router::loadView");
        }
    }

    private static function load($controller, $method, ...$params) {
        $ROOT = $_SERVER["DOCUMENT_ROOT"] . "/app/";
        require_once $ROOT . $controller . ".php";

        $controllerInstance = new  $controller;

        if (!class_exists($controller)) {
            throw new Exception("O controller {$controller} não existe");
        }
        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("O método {$method} não existe no {$controller}");
        }

        // Array associativo com chaves para chamada do método com parâmetros e a requisição JSON
        $REQUEST = json_decode(file_get_contents("php://input"), true);

        $data = array([ // {"HttpRequest":[],"UriParams":[]}
            "HttpRequest" => (empty($REQUEST) ? [] : $REQUEST),
            "UriParams" =>  (empty($params[0]) || $params[0] === [null] ? [] : $params[0]),
        ]);

        // echo Http::toJSON( $data );
        call_user_func_array([$controllerInstance, $method], $data);
    }

    public static function execute() {
        try {
            $routes = self::routes();
            $method = Http::method();
            $uri = Http::requestURI("path");

            if ($uri === "/0")
                self::loadView(["status" => 404]);
            if ($uri === "/contato") // => PAGINA DE CONTATO BLOQUEADA - nao finalizada
                self::loadView(["status" => 503]);


            // Roteamento
            if (isset($routes[$method])) {
                $pageMethod = (empty($method) ? "notfound-HttpMethod" : array_keys($routes[$method])); // Rotas suportadas pelo Métodos HTTP atual
                $router_404 = (isset($routes[$method]['_notfound_']) ? $routes[$method]['_notfound_'] : "");

                if ( isset($_GET["status"]) && $_GET["status"] != "") { // ?status= é enviado pelo Apache com .htaccess
                    if ($_GET["status"] == "404" && $method === "get"){ 	// metodo GET
                        $router_404();
                        exit;
                    }

                    throw new Exception($_GET["status"]);
                }

                if (!in_array($uri, $pageMethod)) {
                    // Verifica se a rota é dinâmica
                    foreach ($routes[$method] as $route_uri => $callback) {
                        if (preg_match("~^$route_uri$~", $uri, $params)) {

                            // $route_uri = rota conhecida no array de rotas
                            array_shift($params);  // Remove o primeiro elemento que corresponde à rota completa

                            // Encontra a última ocorrência de '/'
                            $lastSlashPos = strrpos($uri, '/');

                            // Verifica se a última barra é a última posição da string e remove o último elemento após a barra
                            if ($lastSlashPos !== false && $lastSlashPos < strlen($uri) - 1) {
                                $uri = substr($uri, 0, $lastSlashPos + 1);
                            }

                            $router = $routes[$method][$route_uri];
                            empty($params) ? $router() : $router(...$params);
                            exit;
                        }
                    }

                    // se método identificado && igual a GET && rota NÃO existe para o método
                    $router_404();
                    exit;
                }

                $router = (isset($routes[$method][$uri]) ? $routes[$method][$uri] : $router_404);
                if (!is_callable($router)) {
                    Http::status(404);
                    throw new Exception("Impossível executar a rota '{$uri}' <br>Se o problema persistir, entre em contato com o administrador.");
                }

                // token CSRF
                token_create();
                token_validate();

                // Roteamento > exibição das páginas
                $router();

            }else { // método não identificado na rota
                Http::status(405);
                throw new Exception("Método Não Permitido: {$method}");
            }


        }catch (\Throwable $th) {
            $message = $th->getLine() . " => " . $th;
            $message_user = $th->getMessage();
            Http::status(503);
            echo $message_user; // Http::toJSON([ "message" => $message_user ]); // "status" => "error"
            // trigger_error($th, E_USER_ERROR); // send error to PHPs error handler
        }
    }
}

Router::execute();
