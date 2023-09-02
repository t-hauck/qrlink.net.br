<?php
/*
=> MENSAGENS DE RETORNO HTTP UTILIZADOS
        [1] message => `string`
            Usado para enviar uma mensagem para o javascript mostrar na tela, veja um exemplo na função "get_Stats"
        [2] status => delete
            Usado para identificar para a página de Administração que um link foi apagado com sucesso do banco de dados
        [3] status => deleted
            Usado para identificar para a página de Estatísticas um link que foi apagado do banco de dados
        [4] status => error
            Usado para identificar erro em alguma operação, verifique por "error" ou qualquer coisa diferente do esperado
        [5] status => error-redirect
            Usado para identificar erro especificamente na função "redirectLink"
*/





require_once "conectar_SQL.php";
require_once "view/info.php";

class LinkController {
    private $URL;
    private $conexao;
    private $system_ShortCode;

    public function __construct() {
        $this->URL = check_URLPath();
        $this->conexao = Conectar::sql();
        $this->system_ShortCode = $this->get_SystemShortCode();
    }


    public function urlBlocked($action, $check_URL):bool { // verificar se URL está bloqueada
        if ($action == "check"){
            $sql = "SELECT blocked_url FROM url_shorten WHERE original_url = :url OR short_code = :slug";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":url", $check_URL, PDO::PARAM_STR);
            $stmt->bindParam(":slug", $check_URL, PDO::PARAM_STR);
            $stmt->execute();
            
            $arr = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($arr) return $arr["blocked_url"];
            return false; // FALSE se a URL não estiver bloqueada
        }
        else if ($action == "block"){
            if ($this->urlBlocked("check", $check_URL) === FALSE) {
                $blockType = false;
            }else { 
                $blockType = true; // DESBLOQUEAR
            }

            $sql = "UPDATE url_shorten SET blocked_url = $blockType WHERE original_url = :url OR short_code = :slug";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(":url", $check_URL, PDO::PARAM_STR);
            $stmt->bindParam(":slug", $check_URL, PDO::PARAM_STR);
            $sqlResult = $stmt->execute();

            if (!$sqlResult) return false;
            return $sqlResult; 
        }
    }

    public function Link($REQUEST_link, $REQUEST_passwd){ // $url = (object) $REQUEST["inputURL"]
        $url = addslashes($REQUEST_link);
        $passwd = addslashes($REQUEST_passwd);

        if(parse_url($url, PHP_URL_SCHEME) && parse_url($url, PHP_URL_HOST)) { // if (filter_var($url, FILTER_VALIDATE_URL)) {

            // Verifica se o link contém o domínio atual do sistema
            $FQDN = $_SERVER['HTTP_HOST'];
            if(strpos($url, $FQDN) !== false) {
                header("Content-type: application/json");
                echo json_encode([ "status" => "error", "message" => "Você não pode encurtar uma URL deste site. <br><br>O QRLink pode ser acessado pelo endereço <span style='text-decoration:underline;'>$FQDN</span>" ]) ;
                exit;
            }
            
            // Encurtamento de Links
            if ($this->urlBlocked("check", $url) === FALSE) {
                if ($this->get_new_shortURL($url)) {
                    header("Content-type: application/json");
                    echo json_encode([ "short_code" => $this->get_new_shortURL($url) ]) ;
                }
                else if ($this->save_new_shortURL($url, $passwd)) {
                    header("Content-type: application/json");
                    echo json_encode([ "short_code" => $this->get_new_shortURL($url) ]) ;
                }
                else { // retorno FALSE da verificação feita pela função "save_new_shortURL"
                    header("Content-type: application/json");
                    echo json_encode([ "status" => "error", "message" => "A senha de acesso é inválida. <br>Tente novamente." ]) ;
                }
            }else {
                header("Content-type: application/json");
                echo json_encode([ "status" => "error", "message" => "Este endereço não é permitido." ]) ;
            }
        }else  { // return FALSE;
            header("Content-type: application/json");
            echo json_encode([ "status" => "error", "message" => "A URL informada é inválida. <br>Tente novamente." ]) ;
        }
        exit;
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

        if (empty($passwd) || strlen($passwd) > 0 && strlen(trim($passwd)) == 0) { // se NÃO foi informada uma senha ou enviada com espaços em branco
            $sql = "INSERT INTO url_shorten (original_url, short_code) VALUES (:original_url, :short_code)";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':original_url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
            $sqlResult = $stmt->execute();
        } else { // Senha Válida com pelo menos 1 caractere
            $hash_options = ['cost' => 8];
            $passwd = password_hash($passwd, PASSWORD_DEFAULT, $hash_options); // o hash será diferente a cada execução

            $sql = "INSERT INTO url_shorten (original_url, short_code, short_code_password) VALUES (:original_url, :short_code, :passwd)";
            
            $stmt = $this->conexao->prepare($sql);
            $stmt->bindParam(':original_url', $url, PDO::PARAM_STR);
            $stmt->bindParam(':short_code', $short_code, PDO::PARAM_STR);
            $stmt->bindParam(':passwd', $passwd, PDO::PARAM_STR);
            $sqlResult = $stmt->execute();
        }

        if (!$sqlResult) return false;
        return $sqlResult;    
    }
    private function generateUniqueID():string {
        $link_code = substr(md5(uniqid(rand(), TRUE)), 0, 6);
        $sql = "SELECT * FROM url_shorten WHERE short_code = '" . $link_code . "' "; 
        
        $sqlResult = $this->conexao->query($sql);
        if (!$sqlResult) return false;

        $num_rows = $sqlResult->fetchColumn();
        if ($num_rows > 0) {
            $this->generateUniqueID();
        } else {
            return $link_code;
        }
    }
    

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

    public function get_Stats($link_code) {
        $link_code = addslashes($link_code);
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

                        header("Content-type: application/json");
                        echo json_encode([ $systemArray ]);
                    }
                }
                foreach ($arr as $item) { // Tratamento para exibição da senha para o usuário
                    if ($item["short_code_password"] == NULL) { // LINK NÃO TEM SENHA == NULL
                        $passwdArray = [
                            "url" => $item["original_url"],
                            "short_code" => $item["short_code"],
                            "access" => $item["access"],
                            "last_access" => $item["last_access"],
                            "short_code_password" => $item["short_code_password"] // NULL
                        ];
                        header("Content-type: application/json");
                        echo json_encode([ $passwdArray ]);
                    } else { // FOI CADASTRADA UMA SENHA DE ACESSO PARA O LINK
                        $passwdArray = [
                            "url" => $item["original_url"],
                            "short_code" => $item["short_code"],
                            "access" => $item["access"],
                            "last_access" => $item["last_access"],
                            "short_code_password" => "-", // não enviar o HASH da senha
                            "password_access_attempts" => $item["access_attempts"],         // NULL
                            "password_last_access_attempt" => $item["last_access_attempt"], // NULL
                        ];
                        header("Content-type: application/json");
                        echo json_encode([ $passwdArray ]);
                    }
                }
            }else { // $this->urlBlocked == FALSE
                header("Content-type: application/json");
                echo json_encode([ "status" => "error", "message" => "Não foram encontrados dados sobre o código <span style='text-decoration:underline;'>$link_code</span> <br><br>Verifique se este é um link curto válido. Insira no campo de texto o código curto que foi gerado para o seu link quando foi encurtado, ou o endereço completo para o acesso. Exemplo: $this->system_ShortCode" ]) ;
            }
        }else { // $num_rows return FALSE;
            header("Content-type: application/json");
            echo json_encode([ "status" => "error", "message" => "Não foram encontrados dados sobre o código <span style='text-decoration:underline;'>$link_code</span> <br><br>Verifique se este é um link curto válido. Insira no campo de texto o código curto que foi gerado para o seu link quando foi encurtado, ou o endereço completo para o acesso. Exemplo: $this->system_ShortCode" ]) ;
        }
        exit;
    }
    
    
    public function get_userStats($slug) {
        // $removeKey = array_search($this->system_ShortCode, $slug);
        // if($removeKey !== false){ unset($slug[$removeKey]); }

        $codeData = array();
        $placeholders = implode(',', array_fill(0, count($slug), '?'));    
        $sql = "SELECT * FROM url_shorten WHERE short_code IN ($placeholders) LIMIT 200";
        $stmt = $this->conexao->prepare($sql);

        if (count($slug) > 0) {
            foreach ($slug as $index => $code) {
                $stmt->bindValue($index + 1, $code, PDO::PARAM_STR);
            }
        }
    
        $stmt->execute();
        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($slug as $code) { // slug == dados do usuário
            $found = false;

            foreach ($arr as $item) { // arr == dados do servidor
                if ($item["short_code"] === $code) {
                    $found = true;

                    // se o short_code armazenado no LocalStorage é igual ao do sistema NÃO enviar dados sobre seu link curto
                    if ($item["short_code"] == $this->system_ShortCode){ // instrução para remover código do LocalStorage
                        array_push($codeData, array("short_code" => $code, "status" => "deleted"));
                    } else{

                        if ($this->urlBlocked("check", $code) === FALSE) {
                            // Tratamento para exibição da senha para o usuário, não há tratamento no PHP para "último acesso"
                            if ($item["short_code_password"] == NULL) { // LINK NÃO TEM SENHA
                                $passwdArray = [
                                    "url" => $item["original_url"],
                                    "short_code" => $item["short_code"],
                                    "access" => $item["access"],
                                    "last_access" => $item["last_access"],
                                    "short_code_password" => $item["short_code_password"], // NULL
                                ];
                            } else { // FOI CADASTRADA UMA SENHA DE ACESSO PARA O LINK
                                if ($item["access_attempts"] == NULL) {
                                        $value_PasswordAccessAttempts = "-";
                                }else { $value_PasswordAccessAttempts = $item["access_attempts"]; }
                                if ($item["last_access_attempt"] == NULL) {
                                        $value_PasswordLastAccessAttempt = "-";
                                }else { $value_PasswordLastAccessAttempt = $item["last_access_attempt"]; }

                                $passwdArray = [
                                    "url" => $item["original_url"],
                                    "short_code" => $item["short_code"],
                                    "access" => $item["access"],
                                    "last_access" => $item["last_access"],
                                    "short_code_password" => "-", // não enviar o HASH da senha
                                    "password_access_attempts" => $value_PasswordAccessAttempts,        // default = NULL
                                    "password_last_access_attempt" => $value_PasswordLastAccessAttempt, // default = NULL
                                ];
                            }
                            array_push($codeData, $passwdArray);
                        }else {// $this->urlBlocked
                            array_push($codeData, array("short_code" => $code, "status" => "deleted"));
                        }
                    }

                    break;
                }
            } // foreach
            if (!$found) { // se um link não for encontrado, NÃO enviar informações sobre ele
                array_push($codeData, array("short_code" => $code, "status" => "deleted"));
            }
        } // foreach
        header("Content-type: application/json");
        echo json_encode([ $codeData ]);
        exit();
    }


    // Obter o texto curto informado na URL e redirecionar o usuário 
    public function redirectLink($method, $short_code, $passwd){
        $short_code = addslashes($short_code);
        $passwd = addslashes($passwd);

        $sql = "SELECT original_url, short_code_password FROM url_shorten WHERE short_code = :slug";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindParam(":slug", $short_code, PDO::PARAM_STR);
        $stmt->execute();

        $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        if ($method == "GET") { // GET => /bb0376

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
                        require_once 'view/redirect_pass.php'; // ..e o ELSE IF abaixo verifica a senha
                        exit;
                    }
                }else { // LINK BLOQUEADO = acesso não permitido => // $this->urlBlocked
                    $_SESSION['DB_URLNotFound'] = TRUE;
                }
            } else { // LINK NÃO ENCONTRADO, rotas.php continua com erro 404
                $_SESSION['DB_URLNotFound'] = TRUE;
            }
        } else if ($method == "POST"){ // POST => /checkPassword/78ad6c
            if (password_verify($passwd, $arr[0]["short_code_password"]) ) { // Senha Válida
                if ( $this->redirect_updateDB($short_code) ) {
                    header("Content-type: application/json"); // Front-End redireciona o usuário
                    echo json_encode([ "original_url" => $arr[0]["original_url"] ]) ;
                    exit;
                } else { // ERRO ao atualizar os acessos no banco
                    header("Content-type: application/json");
                    echo json_encode([ "status" => "error-redirect", "message" => "Falha no Banco de Dados" ]) ;
                    exit;
                }
            } else { // SENHA INCORRETA
                if ( $this->redirect_updateDB_PassFailed($short_code) ) {
                    header("Content-type: application/json");
                    echo json_encode([ "status" => "error-redirect", "message" => "Senha Inválida" ]) ;
                    exit;
                } else { // ERRO ao atualizar os acessos no banco
                    header("Content-type: application/json");
                    echo json_encode([ "status" => "error-redirect", "message" => "Falha no Banco de Dados" ]) ;
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


    public function contarSalvos():string {
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten")->fetchColumn();

        if ( $this->URL[1] === "admin" ){
            if ($num_rows == 0) return " Nenhum Registro Salvo";
            if ($num_rows == 1) return " 1 Link";
            if ($num_rows >= 2) return " " . $num_rows . " Links";   
        } else {
            if ($num_rows == 0) return " Nenhum Link Encurtado Salvo no Sistema";
            if ($num_rows == 1) return " <span class='is-size-1 has-text-weight-bold'>1</span><br> <span class='is-size-6'>URL Encurtada</span>";
            if ($num_rows >= 2) return "<span class='is-size-1 has-text-weight-bold'>$num_rows</span><br> <span class='is-size-6'>Total de Links</span>";
        }        
    } // contarSalvos

    public function contarSalvos_hoje():string {
        // $day = date("d"); $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE DAY(added_date) = '$day'")->fetchColumn();
        // $num_rows = $this->conexao->query("SELECT COUNT(*) from url_shorten WHERE added_date BETWEEN DATE_FORMAT(NOW(),'%Y-%m-%d 00:00:00') AND DATE_FORMAT(NOW(),'%Y-%m-%d 23:59:59')")->fetchColumn();
        //// COMANDOS ACIMA => não compativeis com SQLite
        $date = date("Y-m-d");
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE added_date BETWEEN '$date 00:00:00' AND '$date 23:59:59'")->fetchColumn();

        if ($num_rows == 0) return "<span class='is-size-1 has-text-weight-bold'>$num_rows</span><br> <span class='is-size-6'>nenhum link salvo hoje</span>"; 
        if ($num_rows == 1) return "<span class='is-size-1 has-text-weight-bold'>$num_rows</span><br> <span class='is-size-6'>Encurtado Hoje</span>"; // adicionado, salvo
        if ($num_rows >= 2) return "<span class='is-size-1 has-text-weight-bold'>$num_rows</span><br> <span class='is-size-6'>Links Encurtados Hoje</span>";
    } // contarSalvos

    public function obterNomeBanco():string {
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $connectionType = $env["databaseType"];
        if ($connectionType == "sqlite") return "SQLite";
        if ($connectionType == "mysql") return "MySQL";
    } // obterNomeBanco

    public function systemEnvironment() {
        if (file_exists('/.dockerenv')) {

            return "<p>O código e o banco de dados do sistema estão neste momento em um ambiente Docker.</p>";
            // return "Docker";
        }else {
            return "";
        }
    } // systemEnvironment
}













class LinkAdmin { // classe administrativa => /admin
    private $conexao;
    public function __construct() {
        $this->conexao = Conectar::sql();
    }


    public function listarTodos():array {
        $list = array();
        $sql = "SELECT * FROM url_shorten";
        
        $sqlResult = $this->conexao->query($sql);
        if (!$sqlResult) return false;

        while ($link = $sqlResult->fetchObject()) {
            array_push($list, $link);
        }

        return $list;
    } // listarTodos

    public function apagarLINKS() { ///// Apagar TODOS os links do banco de dados
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $databaseType = $env["databaseType"]; // SQLite não possui "TRUNCATE", que é melhor que DELETE

        if ($databaseType === "sqlite") $sqlResult = $this->conexao->query('DELETE FROM url_shorten');
        else $sqlResult = $this->conexao->query('TRUNCATE TABLE url_shorten');

        // header("Content-type: application/json");
        if (!$sqlResult) {
                    echo json_encode( [ "status" => "error" ]);  exit;
        } else {    echo json_encode( [ "status" => "delete" ]); exit; }
    } // apagarLINKS

    public function apagarItem_pesquisa($REQUEST) { // Apagar ITENS DA PESQUISA
        $slug = addslashes($REQUEST);

        $sql = "DELETE FROM url_shorten WHERE original_url LIKE ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bindValue(1, "%$slug%", PDO::PARAM_STR);
        $sqlResult = $stmt->execute();

        header("Content-type: application/json");
        if (!$sqlResult) {
                    echo json_encode( [ "status" => "error" ]);  exit;
        } else {    echo json_encode( [ "status" => "delete" ]); exit; }
    } // apagarItem_pesquisa

    public function apagarItem($REQUEST) {
        $placeholders = implode(',', array_fill(0, count($REQUEST), '?'));
    
        $sql = "DELETE FROM url_shorten WHERE short_code IN ($placeholders)";
        $stmt = $this->conexao->prepare($sql);
    
        if (count($REQUEST) > 0) {
            foreach ($REQUEST as $index => $code) {
                $stmt->bindValue($index + 1, $code, PDO::PARAM_STR);
            }
        }
    
        $sqlResult = $stmt->execute();
    
        header("Content-type: application/json");
        if (!$sqlResult) {
                    echo json_encode( [ "status" => "error" ]);  exit;
        } else {    echo json_encode( [ "status" => "delete" ]); exit; }
    }
    



//     public function apagarItem($REQUEST) { // Apagar APENAS UM link da tabela pelo seu botão, ou UM ou MAIS itens pelo checkbox
//         // $slug = addslashes($REQUEST);
//         $slug = implode(',', array_map(array($this->conexao,'quote'), $REQUEST));

//         // $sql = "DELETE FROM url_shorten WHERE short_code IN (':short_code')";
//         $sql = "DELETE FROM url_shorten WHERE short_code IN ($slug)";
//         $stmt = $this->conexao->prepare($sql);
//         // $stmt->bindParam(':short_code', $slug, PDO::PARAM_STR);
//         $sqlResult = $stmt->execute();

//         header("Content-type: application/json");
//         if (!$sqlResult) {
//                     echo json_encode( [ "status" => "error" ]);  exit;
//         } else {    echo json_encode( [ "status" => "delete" ]); exit; }
//     } // apagarItem
}
