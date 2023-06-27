 <?php
/*
    Neste arquivo estão apenas algumas váriaveis PHP usadas por dois ou mais arquivos
*/


// Texto usado pela página Sobre e Meus Links, e existe uma cópia no arquivo "isVisible.js"
$sobre_LocalStorage = "LocalStorage é um pequeno espaço de armazenamento de dados dentro do seu navegador web. Ele permite salvar informações, como preferências do usuário e favoritos. \nOs dados armazenados nele estão sob total controle seu. \n\nSe você apagar os dados do LocalStorage, o sistema QR-Link não poderá identificar os links que você encurtou para mostrar dados sobre eles. O QR-Link não possui login e cadastro de conta.";


// $URL = str_replace( "?" . $_SERVER['QUERY_STRING'], '', $_SERVER['REQUEST_URI'] );
function check_URLPath() {
    return explode("/", parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH));
}
?>

