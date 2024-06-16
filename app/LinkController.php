<?php
/*
 * => MENSAGENS DE RETORNO HTTP UTILIZADOS
 *        [1] message => `string`
 *            Usado para enviar uma mensagem para o javascript mostrar na tela, veja um exemplo na função "get_Stats"
 *        [2] status => deleted
 *            Usado para identificar para a página de "Estatísticas" um link que foi apagado do banco de dados
 *        [3] HTTP Status => 204
 *            Usado para identificar para a página de "Administração" um link que foi Apagado do banco de dados ou Bloqueado
 *        [4] status => error
 *            Usado para identificar erro em alguma operação, verifique por "error" ou qualquer coisa diferente do esperado
 *        [5] status => error-redirect
 *            Usado para identificar erro especificamente na função "redirectLink"
 */





$ROOT = $_SERVER["DOCUMENT_ROOT"] . "/app";
// $folder_pages = $ROOT . "/view/pages/";
require_once $ROOT . "/../index.php";
require_once $ROOT . "/database/Conectar.php";
require_once $ROOT . "/helpers/Validate.php";
require_once $ROOT . "/helpers/Http.php";


class LinkController {
    public function __construct() {
        // global $folder_pages;
        // $this->pagesFolder = $folder_pages;

        $this->method = Http::method();
        $this->URL = Http::requestURI();

        $this->conexao = Conectar::sql();
        $this->system_ShortCode = $this->get_SystemShortCode();
    }


    private function checkFuncArgs($data) {
        $backtrace = debug_backtrace(); // A segunda posição no array contém informações sobre a função que chamou esta função
        $origin = $backtrace[1]['function'];

        if (empty($origin) === FALSE) {
            if(is_array($data)){
                if (array_key_exists("HttpRequest", $data) && array_key_exists("UriParams", $data)){
                    return $data;   // if ($origin === "redirectLink") return $data["UriParams"][0];
                }
                 throw new Exception("[checkFuncArgs] Parâmetro inválido recebido em '$origin'");
            } // throw new Exception("[checkFuncArgs] Parâmetro recebido por '$origin' não é um array");
        } else {
            throw new Exception('[checkFuncArgs] Origem Desconhecida');
        }
    }

    private function urlBlocked($action, $slug):bool { // verificar se URL está bloqueada
        if ($action == "check"){
            $sql = "SELECT blocked FROM url_shorten WHERE original_url = :url OR short_code = :slug";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":url", $slug, PDO::PARAM_STR);
            $stmt->bindParam(":slug", $slug, PDO::PARAM_STR);
            $stmt->execute();

            $arr = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($arr) return $arr["blocked"];
            return false; // FALSE se a URL não estiver bloqueada
        }
        else if ($action == "block"){
            $this->conexao->beginTransaction();
            try {
                foreach ($slug as $item) {
                    if ( filter_var($item, FILTER_SANITIZE_FULL_SPECIAL_CHARS) ) {

                        $blockType = $this->urlBlocked("check", $item);

                        if ($blockType === false) {
                            $blockType = true;  // BLOQUEAR
                        } else {
                            $blockType = false; // DESBLOQUEAR
                        }

                        $sql = "UPDATE url_shorten SET blocked = :blockType WHERE original_url = :url OR short_code = :slug";
                        $stmt = $this->conexao->prepare($sql);
                        $stmt->bindParam(":blockType", $blockType, PDO::PARAM_BOOL);
                        $stmt->bindParam(":url", $item, PDO::PARAM_STR);
                        $stmt->bindParam(":slug", $item, PDO::PARAM_STR);
                        $stmt->execute();

                        $sql = "UPDATE url_shorten SET last_block = CURRENT_TIMESTAMP WHERE original_url = :url OR short_code = :slug";
                        $stmt = $this->conexao->prepare($sql);
                        $stmt->bindParam(":url", $item, PDO::PARAM_STR);
                        $stmt->bindParam(":slug", $item, PDO::PARAM_STR);
                        $stmt->execute();
                    }
                }

                $this->conexao->commit();
                return true;
            } catch (Exception $e) {
                $this->conexao->rollBack();
                return false;
            }
        }
    }

    public function Link($data){
        $data = $this->checkFuncArgs($data)["HttpRequest"];

        $REQUEST_link = $data["linkURL"];
        $REQUEST_passwd = $data["linkPasswd"];

        $url = $REQUEST_link;
        $passwd = addslashes(trim($REQUEST_passwd));

        if(validate_url($url)) {
            // Verifica se o link contém o domínio atual do sistema
            $FQDN = $_SERVER['HTTP_HOST'];
            if(strpos($url, $FQDN) !== false) {
                throw new Exception("Você não pode encurtar uma URL deste site. <br><br>O QRLink pode ser acessado pelo endereço <span class='is-underlined'>$FQDN</span>");
            }

            // Encurtamento de Links. Caso link já exista, apenas retorna o existente - poderia ser retornado status 409 "Conflict";
            if ($this->urlBlocked("check", $url) === FALSE) {
                if ($this->get_new_shortURL($url)) {
                    echo Http::toJSON([ "short_code" => $this->get_new_shortURL($url) ]);
                    exit;
                }
                else if ($this->save_new_shortURL($url, $passwd)) {
                    echo Http::toJSON([ "short_code" => $this->get_new_shortURL($url) ]);
                    exit;
                }
                else { // retorno FALSE da verificação feita pela função "save_new_shortURL"
                    echo Http::toJSON([ "status" => "error", "message" => "A senha de acesso é inválida. <br>Tente novamente."]);
                    exit;
                }
            }else {
                echo Http::toJSON([ "status" => "error", "message" => "Este endereço não é permitido."]);
                exit;
            }
        }else  { // return FALSE;
            echo Http::toJSON([ "status" => "error", "message" => "A URL informada é inválida. <br>Tente novamente."]);
            exit;
        }
    }
    private function get_new_shortURL($url):string {
        $sql = "SELECT short_code FROM url_shorten WHERE original_url = :url";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":url", $url, PDO::PARAM_STR);
        $stmt->execute(); // será retornado apenas um resultado do banco

        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$arr) return false;

        return $arr[0]["short_code"];
    }

    private function save_new_shortURL($url, $passwd):bool {
        $short_code = $this->generateUniqueID();

        if (validate_password($passwd)) { // Senha Válida com pelo menos 1 caractere
            $hash_options = ['cost' => 8];
            $passwd = password_hash($passwd, PASSWORD_DEFAULT, $hash_options); // o hash será diferente a cada execução

            $sql = "INSERT INTO url_shorten (original_url, short_code, short_code_password) VALUES (:original_url, :short_code, :passwd)";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':original_url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
            $stmt->bindParam(':passwd', $passwd, PDO::PARAM_STR);
            $sqlResult = $stmt->execute();
        }else { // se NÃO foi informada uma senha ou enviada com espaços em branco
            $sql = "INSERT INTO url_shorten (original_url, short_code) VALUES (:original_url, :short_code)";

            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':original_url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
            $sqlResult = $stmt->execute();
        }

        if (!$sqlResult) return false;
        return $sqlResult;
    }

    private function generateUniqueID(): string { // novo código para evitar códigos curtos DUPLICADOS
        $codeLength = 1;              // Quantidade de caracteres inicial usada para criação do código curto
        $maxExecutionTime = 10;       // Tempo máximo de execução da função em segundos
        $startTime = microtime(true); // Tempo de início da execução

        while (true) {
            // Verifica o tempo decorrido
            $currentTime = microtime(true);
            $elapsedTime = $currentTime - $startTime;

            // Se o tempo decorrido exceder o máximo permitido, aumente o comprimento do código
            if ($elapsedTime >= $maxExecutionTime) {
                $codeLength++;                // Incrementa os caracteres
                $startTime = microtime(true); // Reinicia o tempo de início
            }

            // Gera um código curto com tamanho definido em $codeLength
            $link_code = substr(md5(uniqid(rand(), true)), 0, $codeLength);

            // Verifica se o código já existe no banco de dados
            $sql = "SELECT COUNT(*) FROM url_shorten WHERE short_code = :link_code";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':link_code', $link_code, PDO::PARAM_STR);

            try {
                $stmt->execute();
                $num_rows = $stmt->fetchColumn();

                // Códigos não Permitidos / que geraram problemas no redirecionamento
                $disallowedCodes = ["0", "a"];

                // Retornar código se não existe no banco e verificar se NÃO está no array acima
                if ($num_rows == 0 && in_array($link_code, $disallowedCodes) == FALSE) {
                    return $link_code;
                }
            } catch (PDOException $e) {
                // Log the exception and continue
                trigger_error($e->getMessage(), E_USER_ERROR);
                return false;
            }
        }
    }
    //
    //     private function generateUniqueID():string {
    //         $link_code = substr(md5(uniqid(rand(), TRUE)), 0, 6);
    //         $sql = "SELECT * FROM url_shorten WHERE short_code = '" . $link_code . "' ";
    //
    //         $sqlResult = $this->conexao->query($sql);
    //         if (!$sqlResult) return false;
    //
    //         $num_rows = $sqlResult->fetchColumn();
    //         if ($num_rows > 0) {
    //             $this->generateUniqueID();
    //         } else {
    //             return $link_code;
    //         }
    //     }

    public function get_SystemShortCode() { // apenas retorna o 'short_code' do próprio sistema, para utilizar como exemplo na tela
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $domainName = $_SERVER['HTTP_HOST'] . '/';
        $FQDN = $protocol . $domainName;

        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE original_url = '$FQDN'")->fetchColumn();

        if ($num_rows) {
            $sql = "SELECT short_code FROM url_shorten WHERE original_url = :url";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":url", $FQDN, PDO::PARAM_STR);
            $stmt->execute();

            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $arr[0]["short_code"];
        } else {
            if ($this->save_new_shortURL($FQDN, "")) {
                // Executar novamente esta mesma função para rodar o SELECT no banco
                return $this->get_SystemShortCode();
            } else { return FALSE; }
        }
    }

    public function get_Stats($data) {
        $link_code = $this->checkFuncArgs($data)["HttpRequest"]["linkCode"];
        $link_code = addslashes(trim($link_code));
        $FQDN_StrPos = strpos($link_code, $_SERVER['HTTP_HOST']); // Verifica se foi enviada a URL completa e extrai apenas o código
        $URL_query = explode("/", $link_code);
        if($FQDN_StrPos !== FALSE) {
            if      ($URL_query[3]) $link_code = $URL_query[3]; // https://qrlink.net.br/b4b9df
            else if ($URL_query[1]) $link_code = $URL_query[1]; // qrlink.net.br/b4b9df
        }

        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE short_code = '$link_code'")->fetchColumn();

        if ($num_rows) {
            if ($this->urlBlocked("check", $link_code) === FALSE) {
                $sql = "SELECT * FROM url_shorten WHERE short_code = :slug";

                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(":slug", $link_code, PDO::PARAM_STR);
                $stmt->execute();

                $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($link_code == $this->system_ShortCode) { // retorna dados personalizados caso o código seja o do próprio sistema
                    foreach ($arr as $item) { ///// sistema = SEM SENHA
                        $systemArray = [
                            "url" => $item["original_url"],
                            "short_code" => $item["short_code"],
                            "access" => "0",
                            "last_access" => "-", // "0000-00-00 00:00:00",
                            "short_code_password" => $item["short_code_password"] // NULL
                        ];
                        echo Http::toJSON([ $systemArray ]);
                        exit;
                    }
                }

                echo Http::toJSON($this->process_databaseData($arr));
            }else { // $this->urlBlocked == FALSE
                 echo Http::toJSON([ "status" => "error", "message" => "Não foram encontrados dados sobre o código <span class='is-underlined'>$link_code</span> <br><br>Verifique se este é um link curto válido. Insira no campo de texto o código curto que foi gerado para o seu link quando foi encurtado, ou o endereço completo para o acesso.
                 Exemplo: $this->system_ShortCode" ]);
                 exit;
            }
        }else { // $num_rows return FALSE;
            echo Http::toJSON([ "status" => "error", "message" => "Não foram encontrados dados sobre o código <span class='is-underlined'>$link_code</span> <br><br>Verifique se este é um link curto válido. Insira no campo de texto o código curto que foi gerado para o seu link quando foi encurtado, ou o endereço completo para o acesso. Exemplo: $this->system_ShortCode" ]);
            exit;
        }
    }

    public function get_userStats($data) {
        $data = $this->checkFuncArgs($data)["HttpRequest"];

        $dataServer = [];  // Array de dados do Servidor para retorno ao Usuário

        $placeholders_url = [];
        $placeholders_code = [];

        // foreach ($data as $item) { // Extrai 'short_code' e 'original_url' apenas se URL for válida
        foreach ($data as $key => $item) {
            if(parse_url($item["original_url"], PHP_URL_SCHEME) && parse_url($item["original_url"], PHP_URL_HOST)) { // if (filter_var($url, FILTER_VALIDATE_URL)) {
                $placeholders_url[] = $item['original_url'];
                $placeholders_code[] = preg_replace('/[^A-Za-z0-9]/', '', $item["short_code"]);
            }else { // URL Invalida, provavelmente o LocalStorage foi editado manualmente
                unset($data[$key]); // remover array do link do array $data
            }
        }

        if (empty($data) === FALSE) {
            $pholdersUrl = implode(',', array_fill(0, count($placeholders_url), '?'));
            $pholdersCode = implode(',', array_fill(0, count($placeholders_code), '?'));

            $sql = "SELECT * FROM url_shorten WHERE original_url IN ($pholdersUrl) AND short_code IN ($pholdersCode) LIMIT 200";
            $stmt = $this->conexao->prepare($sql);

            // Associa os valores aos placeholders usando bindValue
            foreach ($placeholders_url as $index => $code) {
                $stmt->bindValue($index + 1, $code, PDO::PARAM_STR);
            }
            foreach ($placeholders_code as $index => $code) {
                $stmt->bindValue((count($placeholders_url) + $index + 1), $code, PDO::PARAM_STR);
            }

            $stmt->execute();
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach (array_combine($placeholders_code, $placeholders_url) as $user_code => $user_url) { // dados do usuário
                $found = false;

                foreach ($arr as $link_server) { // arr == dados do servidor
                    if ($link_server["short_code"] === "$user_code" && $link_server["original_url"] === "$user_url") {
                        $found = true;

                        // se o short_code armazenado no LocalStorage é igual ao do sistema NÃO enviar dados sobre seu link curto
                        if ($link_server["short_code"] == $this->system_ShortCode){ // instrução para remover código do LocalStorage
                            // array_push($dataServer, array("short_code" => $user_url, "status" => "deleted"));
                            array_push($dataServer, array("original_url" => $user_url, "short_code" => $user_code, "status" => "deleted"));
                        } else{

                            if ($this->urlBlocked("check", $user_url) === FALSE) {
                                foreach ($this->process_databaseData($arr) as $item) {
                                    if (!in_array($item, $dataServer)) array_push($dataServer, $item);
                                }
                            }else { // link bloqueado
                                array_push($dataServer, array("original_url" => $user_url, "short_code" => $user_code, "status" => "deleted"));
                            }
                        }

                        break;
                    }
                } // foreach
                if (!$found) { // se um link não for encontrado, NÃO enviar informações sobre ele
                    array_push($dataServer, array("original_url" => $user_url, "short_code" => $user_code, "status" => "deleted"));
                }
            } // foreach

            echo Http::toJSON($dataServer);
        }else {
            Http::status(204); // 400
        }

        exit;
    }

    // Obter o texto curto informado na URL e redirecionar o usuário
    public function redirectLink($data){
        $method = $this->method;
        $short_code = $this->checkFuncArgs($data)["UriParams"];
        $short_code = addslashes($short_code);

        $sql = "SELECT original_url, short_code_password FROM url_shorten WHERE short_code = :slug";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(':slug', $short_code, PDO::PARAM_STR);
        $stmt->execute();

        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($method == "get") { // GET => /bb0376

            if (!empty($arr)) { // verifica se $arr não está vazio = SQL SELECT retornou dados
                if ($this->urlBlocked("check", $short_code) === FALSE) {

                    if ($arr[0]["short_code_password"] == NULL) {
                        if ( $this->redirect_updateDB($short_code) ) {
                            header("Location:" . $arr[0]["original_url"] );
                            exit;
                        } else {
                            header("Location: /");
                            exit;
                        }
                    } else { // EXIBIÇÃO html DO FORMULÁRIO PARA ACESSO A UM LINK PROTEGIDO POR SENHA
                        Router::loadView("link_redirect_password.php"); // ..e o ELSE IF abaixo verifica a senha
                        exit;
                    }
                }else { // LINK BLOQUEADO = acesso não permitido => // $this->urlBlocked
                    Router::loadView(["status" => 403]);
                }
            } else { // LINK NÃO ENCONTRADO, rotas continua com erro 404
                Router::loadView(["status" => 404]);
            }
        } else if ($method == "post"){ // POST => /checkPassword/78ad6c
            $data = $this->checkFuncArgs($data)["UriParams"];
            $passwd = isset($data["linkPasswd"]) ? $data["linkPasswd"] : "nao-definida";
            $passwd = addslashes($passwd);

            if (password_verify($passwd, $arr[0]["short_code_password"]) ) { // Senha Válida
                if ( $this->redirect_updateDB($short_code) ) { // Front-End redireciona o usuário
                    echo Http::toJSON([ "original_url" => $arr[0]["original_url"] ]);
                    exit;
                } else { // ERRO ao atualizar os acessos no banco
                    echo Http::toJSON([ "status" => "error-redirect", "message" => "Falha no Banco de Dados ao validar Senha" ]);
                    exit;
                }
            } else { // SENHA INCORRETA
                if ( $this->redirect_updateDB_PassFailed($short_code) ) {
                    echo Http::toJSON([ "status" => "error-redirect", "message" => "Senha Inválida" ]);
                    exit;
                } else { // ERRO ao atualizar os acessos no banco
                    echo Http::toJSON([ "status" => "error-redirect", "message" => "Falha no Banco de Dados" ]);
                    exit;
                }
            }
        }
    }

    private function redirect_updateDB_PassFailed($slug):bool { // Atualizar banco de dados para tentativas de acesso a links com SENHA
        $slug = addslashes($slug);
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE short_code = '$slug'")->fetchColumn();

        if ($num_rows) {
            $sql = "SELECT * FROM url_shorten WHERE short_code = :slug";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":slug", $slug, PDO::PARAM_STR);
            $stmt->execute();

            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($arr as $row) {
                $hits = $row['access_attempts'] + 1;
                $sql = "UPDATE url_shorten SET access_attempts = :access_attempts WHERE id = :id";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(':access_attempts', $hits, PDO::PARAM_INT);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                $sql = "UPDATE url_shorten SET last_access_attempt = CURRENT_TIMESTAMP WHERE id = :id";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                return TRUE;
            }
        } else {
            return FALSE;
        }
    }

    private function redirect_updateDB($slug):string { // Atualizar os acessos da URL no banco de dados
        $slug = addslashes($slug);
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE short_code = '$slug'")->fetchColumn();

        // de acordo com o 'INSERT' NO BANCO, será salvo e retornado na consulta apenas um resultado
        if ($num_rows) {
            $sql = "SELECT * FROM url_shorten WHERE short_code = :slug";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":slug", $slug, PDO::PARAM_STR);
            $stmt->execute(); // $stmt->rowCount(); => contar resultados

            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($arr as $row) {
                // Atualizar coluna da Quantidade de acessos da URL
                $hits = $row['access'] + 1;
                $sql = "UPDATE url_shorten SET access = :access WHERE id = :id";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(':access', $hits, PDO::PARAM_INT);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                // Atualizar coluna do Último acesso com a data atual
                $sql = "UPDATE url_shorten SET last_access = CURRENT_TIMESTAMP WHERE id = :id";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                return $row["original_url"]; // Retorno da URL completa para o front-end
            }
        } else {
            return FALSE;
        }
    }

    private function process_databaseData($data):array {
        $processedData = [];

        foreach ($data as $item) {
            if ($item["short_code_password"] == NULL) { // LINK NÃO TEM SENHA == NULL
                $passwdArray = [
                    "url" => $item["original_url"],
                    "short_code" => $item["short_code"],
                    "access" => $item["access"],
                    "last_access" => $item["last_access"],
                    "short_code_password" => $item["short_code_password"],           // NULL
                    //"added_date" => (in_array("admin", $this->URL)) ? $item["added_date"] : null,
                ];
            } else { // FOI CADASTRADA UMA SENHA DE ACESSO PARA O LINK
                $passwdArray = [
                    "url" => $item["original_url"],
                    "short_code" => $item["short_code"],
                    "access" => $item["access"],
                    "last_access" => $item["last_access"],
                    "short_code_password" => "-",                                   // não enviar o HASH da senha
                    "password_access_attempts" => $item["access_attempts"],         // default = NULL
                    "password_last_access_attempt" => $item["last_access_attempt"], // default = NULL
                    //"added_date" => (in_array("admin", $this->URL)) ? $item["added_date"] : null,
                ];
            }
            // adicionar Mais Dados se estiver na página administrativa
            if (strpos($this->URL, "admin") !== false) {
                $passwdArray["blocked"] = $item["blocked"];
                $passwdArray["added_date"] = $item["added_date"];
                $passwdArray["blocking_date"] = $item["blocking_date"];
                $passwdArray["last_block"] = $item["last_block"];
            }

            $processedData[] = $passwdArray;
        }
        return $processedData;
    }








    ////
    // SEÇÃO DE ESTATÍSTICAS
    ////
    public function contarSalvos():int {
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten")->fetchColumn();
        return $num_rows;
    } // contarSalvos

    private function contarSalvos_bloqueados($type):int {
        $search = $type ? 1 : 0;
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE blocked = $search")->fetchColumn();
        return $num_rows;
    } // contarSalvos_bloqueados

    private function contarSalvos_hoje():int {
        // $day = date("d"); $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE DAY(added_date) = '$day'")->fetchColumn();
        // $num_rows = $this->conexao->query("SELECT COUNT(*) from url_shorten WHERE added_date BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')")->fetchColumn();
        //// COMANDOS ACIMA => não compativeis com SQLite
        $date = date("Y-m-d");
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE added_date BETWEEN '$date 00:00:00' AND '$date 23:59:59'")->fetchColumn();
        return $num_rows;
    } // contarSalvos

    private function contarAcessos($type):int { // AND short_code <> $this->system_ShortCode
        if ($type == "total") {
            $search = "access"; // coluna do banco de dados
        }else if ($type == "tentativas_com_senha") {
            $search = "access_attempts";
        }else  { $search = "access"; }
        // acessos_links_com_senha

        $num_rows = $this->conexao->query("SELECT SUM($search) FROM url_shorten WHERE blocked = 0")->fetchColumn();
        if (empty($num_rows)) $num_rows = 0; // access_attempts = DEFAULT NULL
        return $num_rows;
    } // contarAcessos totais DESCONSIDERANDO os links bloqueados

    private function contarAcessos_comSenha():int {
        $num_rows = $this->conexao->query("SELECT SUM(access) FROM url_shorten WHERE blocked = 0 AND short_code_password IS NOT NULL")->fetchColumn();
        if (empty($num_rows)) $num_rows = 0; // retorna NULL quando não encontra dados
        return $num_rows;
    } // contarAcessos totais com senha DESCONSIDERANDO os links bloqueados

    private function contarAcessos_hoje():int {
        $date = date("Y-m-d");
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE last_access BETWEEN '$date 00:00:00' AND '$date 23:59:59'")->fetchColumn();
        return $num_rows;
    } // contarAcessos_hoje

    public function obterNomeBanco():string {
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $connectionType = $env["databaseType"];
        if ($connectionType == "sqlite") return "SQLite";
        if ($connectionType == "mysql") return "MySQL";
    } // obterNomeBanco

    public function systemEnvironment():string {
        if (file_exists('/.dockerenv')) {
                return "ambiente Docker";
        }else   return "ambiente de <abbr title='O sistema não está dentro de um container Docker'>Servidor Normal</abbr>";
    } // systemEnvironment


    public function get_ServerStats() {
        $links_totais = $this->contarSalvos();
        $links_salvosHoje = $this->contarSalvos_hoje();
        $links_ativos = $this->contarSalvos_bloqueados(FALSE);
        $links_bloqueados = $this->contarSalvos_bloqueados(TRUE);

        $acessos_totais = $this->contarAcessos("total");
        $acessos_hoje = $this->contarAcessos_hoje();
        $acessos_linksComSenha = $this->contarAcessos_comSenha();
        $acessos_tentativasSenha = $this->contarAcessos("tentativas_com_senha");
        $data = [
            "records" => [
                "total" => $links_totais,
                "saved_today" => $links_salvosHoje,
                "active" => $links_ativos,
                "blocked" => $links_bloqueados
            ],
            "access" => [
                "total" => $acessos_totais,
                "accesses_today" => $acessos_hoje,
                "password_protected_links" => $acessos_linksComSenha,
                "password_access_attempts" => $acessos_tentativasSenha
            ]
        ];

        echo Http::toJSON($data);
        exit;
    } // get_ServerStats









    ////
    // SEÇÃO DE PAGINAÇÃO DOS DADOS EM TABELA
    // => https://code.tutsplus.com/how-to-paginate-data-with-php--net-2928t
    // private function pagination_createLinks($btnLinksPage) {
    //     $last = ceil($this->pagination_table_total / $this->pagination_table_limit);
    //     $start = (($this->pagination_table_page - $btnLinksPage) > 0) ? $this->pagination_table_page - $btnLinksPage : 1;
    //     $end = (($this->pagination_table_page + $btnLinksPage) < $last) ? $this->pagination_table_page + $btnLinksPage : $last;

    //     $html = "";
    //     $html .= '<ul class="pagination-list">';
    //     if ($start > 1) {
    //         $html .= '<li><a class="pagination-link" href="?limit=' . $this->pagination_table_limit . '&page=1" aria-label="Goto page 1">1</a></li>';
    //         $html .= '<li><span class="pagination-ellipsis">&hellip;</span></li>'; // &hellip; == ...
    //     }
    //     for ($i = $start; $i <= $end; $i++) {
    //         $btn_activepage = ($this->pagination_table_page == $i) ? "is-current" : "";
    //         $html .= '<li><a class="pagination-link ' . $btn_activepage . '" href="?limit=' . $this->pagination_table_limit . '&page=' . $i . '" aria-label="Goto page ' . $i . '">' . $i . '</a></li>';
    //     }

    //     if ($end < $last) {
    //         $html .= '<li><span class="pagination-ellipsis">&hellip;</span></li>'; // &hellip; == ...
    //         $html .= '<li><a href="?limit=' . $this->pagination_table_limit . '&page=' . $last . '">' . $last . '</a></li>';
    //     }
    //     $html .= '</ul>';

    //     // Desabilitar botões "Previous" e "Next page" quando necessário
    //     $prev_disabled = ($this->pagination_table_page == 1) ? 'disabled' : '';
    //     $next_disabled = ($this->pagination_table_page == $last) ? 'disabled' : '';

    //     // Remover a tag "a" dos links quando eles estiverem desabilitados
    //     $prev_button = '<a class="pagination-previous"' . $prev_disabled . 'href="?limit=' . $this->pagination_table_limit . '&page=' . ($this->pagination_table_page - 1) . '">Previous</a>';
    //     $next_button = '<a class="pagination-next"' . $next_disabled . 'href="?limit=' . $this->pagination_table_limit . '&page=' . ($this->pagination_table_page + 1) . '">Next page</a>';

    //     if ($prev_disabled) $prev_button = '<span class="pagination-previous"' . $prev_disabled . '>Previous</span>';
    //     if ($next_disabled) $next_button = '<span class="pagination-next"' . $next_disabled . '>Next page</span>';

    //     $html .= $prev_button;
    //     $html .= $next_button;

    //     // return $html;
    //     return $last . "  " .  $start . "   " . $end . "   " . $btnLinksPage .  "   pagination_table_total = " . $this->pagination_table_total . "   pagination_table_limit = " . $this->pagination_table_limit . "  pagination_table_page = " . $this->pagination_table_page . "<br><br> $html";
    // }









    ////
    // SEÇÃO ADMINISTRATIVA
    ////
    private function tableCalcTotalPages($rows, $limit):int {
        return ceil($rows / $limit);
    }


    public function showLinks($data){
        $data = $this->checkFuncArgs($data)["HttpRequest"];

        // obter dados para a tabela de links
        $page = $data["page"];
        $rows = $data["rows"];
        $sort = $data["sort"];
        $search = $data["search"];
        $search_term = $data["search_term"];

        if ($sort !== "asc" && $sort !== "desc") {
            $sort = "desc";
        }
        if (is_numeric($rows) && $rows > 0) {
            // echo $page . "  " .  $limit;
            if ($page === 1) $page = 0;
            $limit_offset = max(0, ($page - 1) * $rows); // ensure offset is non-negative

            if ($search === false)
                    $this->listarTodos($limit_offset, $rows, $sort);
            else    $this->search_link($limit_offset, $rows, $sort, $search_term);
        }else { Http::status(400); }

        exit;
    }

    private function search_link($limit_offset, $limit, $sort, $query): array {
        $condition1 =  $condition2 =  trim($query);
        $sql = "SELECT * FROM url_shorten WHERE original_url LIKE ? OR short_code LIKE ? ORDER BY id $sort LIMIT ?, ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, '%' . $condition1 . '%', PDO::PARAM_STR);
        $stmt->bindValue(2, '%' . $condition2 . '%', PDO::PARAM_STR);
        $stmt->bindValue(3, $limit_offset, PDO::PARAM_INT);
        $stmt->bindValue(4, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $sql_numRows = $stmt->rowCount(); //  "records" => $sql_numRows,

        echo Http::toJSON([
            // "total_pages" => $this->tableCalcTotalPages($this->contarSalvos(), $limit),
            "total_records" => $this->contarSalvos(),
            "rows" => $this->process_databaseData($arr),
        ]);
        exit;
    } // search_link

    private function listarTodos($limit_offset, $limit, $sort): array {
        $sql = "SELECT * FROM url_shorten ORDER BY id $sort LIMIT ?, ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, $limit_offset, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // $sql_numRows = $stmt->rowCount();

        echo Http::toJSON([
            "total_records" => $this->contarSalvos(),
            "rows" => $this->process_databaseData($arr),
        ]);
        exit;
    } // listarTodos

    public function deleteOneLink($data) {
        $slug = $this->checkFuncArgs($data)["UriParams"]; // method DELETE - Apenas UM Link
        if (!is_string($slug)) {
            throw new Exception("Parâmetro inválido recebido em 'deleteOneLink'");
        }

        $sql = "DELETE FROM url_shorten WHERE short_code = :slug";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":slug", $slug, PDO::PARAM_STR);

        $sqlResult = $stmt->execute();

        if (!$sqlResult) {
            throw new Exception("Falha ao executar operação no Banco de Dados");
        } else {
            Http::status(204);
            exit;
        }
    } // deleteOneLink

    public function deleteLinks($data) {
        $slug = $this->checkFuncArgs($data)["HttpRequest"]; // method POST - UM ou MAIS Links
        if (!is_array($slug) || count($slug) == 0) {
            throw new Exception("Parâmetro inválido recebido em 'deleteLinks'");
        }

        $placeholders = implode(',', array_fill(0, count($slug), '?'));
        $sql = "DELETE FROM url_shorten WHERE short_code IN ($placeholders)";
        $stmt = $this->conexao->prepare($sql);

        foreach ($slug as $index => $code) {
            $stmt->bindValue($index + 1, $code, PDO::PARAM_STR);
        }

        $sqlResult = $stmt->execute();

        if (!$sqlResult) {
            throw new Exception("Falha ao executar operação no Banco de Dados");
        } else {
            Http::status(204);
            exit;
        }
    } // deleteLinks

    private function urlBlocking_byDate($blockDate, $slug):bool {
        $this->conexao->beginTransaction();
        try {
            foreach ($slug as $item) {
                $blockType = $this->urlBlocked("check", $item);

                $sql = "UPDATE url_shorten SET blocking_date = :blockingDate WHERE original_url = :url OR short_code = :slug";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bindParam(":blockingDate", $blockDate, PDO::PARAM_STR);
                $stmt->bindParam(":url", $item, PDO::PARAM_STR);
                $stmt->bindParam(":slug", $item, PDO::PARAM_STR);
                $stmt->execute();
            }

            $this->conexao->commit();
            return true;

        } catch (Exception $e) {
            $this->conexao->rollBack();
            return false;
        }
    } // urlBlocking_byDate

    public function blockLink($data) {
        $slug = $this->checkFuncArgs($data)["HttpRequest"]["link"];
        $date = $this->checkFuncArgs($data)["HttpRequest"]["date"];

        if (is_string($date) && !empty($date)) { // Bloquear ou Desbloquear com agendamento
            if (strtotime($date) !== false) {    // Validar data recebida
                if(strtotime($date) < strtotime(date('Y-m-d'))) {
                    echo Http::toJSON([ "status" => "error", "message" => "A data deve ser maior que o dia de hoje. <br>Tente novamente."]);
                    exit;
                }
                $blockDB = $this->urlBlocking_byDate($date, $slug);
            }else {
                echo Http::toJSON([ "status" => "error", "message" => "A URL informada é inválida. <br>Tente novamente."]);
                exit;
            }
        }else { // bloquear ou Desbloquear AGORA
            $blockDB = $this->urlBlocked("block", $slug);
        }

        if ($blockDB === FALSE) {
            throw new Exception("Falha ao executar operação no Banco de Dados em 'blockLink'");
        } else {
            echo Http::status(204);
            exit;
        }
    }

    // public function apagarLINKS() { ///// Apagar TODOS os links do banco de dados
    //     $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
    //     $databaseType = $env["databaseType"]; // SQLite não possui "TRUNCATE"

    //     if ($databaseType === "sqlite") $sqlResult = $this->conexao->query('DELETE FROM url_shorten');
    //     else $sqlResult = $this->conexao->query('TRUNCATE TABLE url_shorten');

    //     if (!$sqlResult) {
    //                 echo Http::toJSON( [ "status" => "error", "message" => "Falha no Banco de Dados" ]);  exit;
    //     } else {    header("HTTP/1.1 204 No Content"); exit; }
    // } // apagarLINKS
}
