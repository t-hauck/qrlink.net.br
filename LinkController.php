<?php

require_once "conectar_SQL.php";

class LinkController {
    private $conexao;
    public function __construct() {
        $this->conexao = Conectar::sql();
    }

    public function Link($REQUEST){ // $url = (object) $REQUEST["inputURL"]
        $url = $REQUEST["inputURL"];

        if ( $this->validURL($url) ) {
            if ($this->get_new_shortURL($url)) {
                //// Código abaixo retorna TODAS as colunas do banco de dados relacionadas ao item
                // $sql_select = $this->get_new_shortURL($url);
                // $result = [$sql_select]; // $result = ["link" => $sql_select];
                // header("Content-type: application/json");
                // echo json_encode( $result );
                // exit();
                ////
                $sql_select = $this->get_new_shortURL($url);
                $result = [$sql_select];
                foreach ($result as $res) { //// Retornar APENAS a URL curta
                    foreach ($res as $item) { // [0]
                        header("Content-type: application/json");
                        echo json_encode([ "short_code" => $item->short_code ]) ;
                        exit();
                    }
                }
            }
            else if ($this->save_new_shortURL($url)) {
                // $sql_insert = $this->Link($url);
                // $result = [$sql_insert];
                // header("Content-type: application/json");
                // echo json_encode( $result ); // resultado do IF ACIMA, que busca os dados
                // exit();
                ////
                $sql_insert = $this->get_new_shortURL($url);
                $result = [$sql_insert];
                foreach ($result as $res) { //// Retornar APENAS a URL curta
                    foreach ($res as $item) { // [0]
                        header("Content-type: application/json");
                        echo json_encode([ "short_code" => $item->short_code ]) ;
                        exit();
                    }
                }
            }
        }
        else  {
            //echo "=> URL Inválida"; exit();
            return false;
        }
    }
    private function get_new_shortURL($url):array {
        $linkMenor = array();

        $sql = "SELECT * FROM url_shorten WHERE url = '" . $url . "' ";
        $sqlResult = $this->conexao->query($sql);

        if (!$sqlResult) return false;

        while ($link = $sqlResult->fetchObject()) {
            array_push($linkMenor, $link);
        } // será retornado apenas um resultado do banco

        return $linkMenor;
    }
    private function save_new_shortURL($url):bool {
        $short_code = $this->generateUniqueID();

        $sql = "INSERT INTO url_shorten (url, short_code, hits) VALUES ('$url', '$short_code', '0')";

        $stmt = $this->conexao->prepare($sql);
        return $stmt->execute();
    }
    private function generateUniqueID():string {
        $token = substr(md5(uniqid(rand(), true)),0,6);
        $sql = "SELECT * FROM url_shorten WHERE short_code = '" . $token . "' "; 
        
        $sqlResult = $this->conexao->query($sql);
        if (!$sqlResult) return false;

        $num_rows = $sqlResult->fetchColumn();
        if ($num_rows > 0) {
            $this->generateUniqueID();
        } else {
            return $token;
        }
    }


    // Obter o texto curto informado na URL e redirecionar o usuário 
    public function redirectLink($REQUEST){
        $url = $this->get_redirectURL($REQUEST);

        if ($url) {
            // echo $_SERVER['HTTP_HOST'] . "<b>/$REQUEST</b> => <i>$url</i>";
            header("Location:" . $url);
            exit;
        } else {
            header("Location: /");
            exit; // echo " else; <br><br> $REQUEST";
        }
    }

    private function get_redirectURL($slug):string { // Atualizar o número de acessos da URL no banco de dados  
        $slug = addslashes($slug);
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten WHERE short_code = '$slug'")->fetchColumn(); 
        
        // de acordo com o 'INSERT' NO BANCO, será salvo e retornado na consulta apenas um resultado
        if ($num_rows) {
            //// http://localhost:9000/6113a8
            //// http://localhost:9000/pagina1/pagina2/pagina3
            
            $query = "SELECT * FROM url_shorten WHERE short_code = :slug";
            $stmt = $this->conexao->prepare($query);
            $stmt->bindParam(':slug', $slug, PDO::PARAM_STR);
            $stmt->execute(); // $stmt->rowCount(); => contar resultados
            
            $arr = $stmt->fetchAll(PDO::FETCH_ASSOC);
 
            foreach ($arr as $row) { // https://stackoverflow.com/questions/883365/row-count-with-pdo
                // Atualizar coluna da Quantidade de acessos da URL
                $hits = $row['hits'] + 1;
                $query = "UPDATE url_shorten SET hits = :hits WHERE id = :id";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindParam(':hits', $hits, PDO::PARAM_INT);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                // Atualizar coluna do Último acesso com a data atual
                $query = "UPDATE url_shorten SET last_acess = CURRENT_TIMESTAMP WHERE id = :id";
                $stmt = $this->conexao->prepare($query);
                $stmt->bindParam(':id', $row['id'], PDO::PARAM_INT);
                $stmt->execute();

                return $row['url']; // Retorno da URL completa para o front-end
            }
        } else {
            return FALSE;
        }
    }

    private function validURL($str):bool {
        $regex = "((https?|ftp)\:\/\/)?";
        $regex .= "([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?";
        $regex .= "([a-z0-9-.]*)\.([a-z]{2,3})";
        $regex .= "(\:[0-9]{2,5})?";
        $regex .= "(\/([a-z0-9+\$_-]\.?)+)*\/?";
        $regex .= "(\?[a-z+&\$_.-][a-z0-9;:@&%=+\/\$_.-]*)?";
        $regex .= "(#[a-z_.-][a-z0-9+\$_.-]*)?";
        
        if (preg_match("/^$regex$/i", $str)) { // $str = 'https://websolutionstuff.com/';
            return true; // Enter URL is a valid URL
        } else {
            return false;
        }
    }

}









class LinkAdmin { // classe administrativa => /qrlink/admin
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

    public function obterNomeBanco():string {
        $env = (parse_ini_file('.env')) ? parse_ini_file('.env') : getenv();
        $connectionType = $env["databaseType"];
        if ($connectionType == "sqlite") return "SQLite";
        if ($connectionType == "mysql") return "MySQL";
    } // obterNomeBanco

    public function contarSalvos():string {
        $num_rows = $this->conexao->query("SELECT COUNT(*) FROM url_shorten")->fetchColumn();

        if ( $_SERVER["REQUEST_URI"] == "/qrlink/admin" ){
            if ($num_rows == 0) return " Nenhum Registro Salvo";
            if ($num_rows == 1) return " 1 Link";
            if ($num_rows >= 2) return " " . $num_rows . " Links";   
        } else {
            if ($num_rows == 0) return " Nenhum Link Encurtado Salvo no Sistema";
            if ($num_rows == 1) return " 1 URL Encurtada";
            if ($num_rows >= 2) return " " . $num_rows . " Links Encurtados Salvos";  
        }        
    } // contarSalvos

    public function apagarLINKS() { ///// Apagar TODOS os links da tabela
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
        $slug = $REQUEST["search"];

        $slug = addslashes($slug);
        $query = "DELETE FROM url_shorten WHERE url LIKE ?";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindValue(1, "%$slug%", PDO::PARAM_STR);
        $sqlResult = $stmt->execute();

        header("Content-type: application/json");
        if (!$sqlResult) {
                    echo json_encode( [ "status" => "error" ]);  exit;
        } else {    echo json_encode( [ "status" => "delete" ]); exit; }
    } // apagarItem_pesquisa

    public function apagarItem($REQUEST) { // Apagar APENAS UM link da tabela pelo seu botão
        $slug = $REQUEST["short_code"];

        $slug = addslashes($slug);
        $query = "DELETE FROM url_shorten WHERE short_code = :short_code";
        $stmt = $this->conexao->prepare($query);
        $stmt->bindParam(':short_code', $slug, PDO::PARAM_STR);
        $sqlResult = $stmt->execute();

        header("Content-type: application/json");
        if (!$sqlResult) {
                    echo json_encode( [ "status" => "error" ]);  exit;
        } else {    echo json_encode( [ "status" => "delete" ]); exit; }
    } // apagarItem
}
