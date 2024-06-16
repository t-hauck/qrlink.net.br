<?php
function get_http_status_message($code) {
    $http_status_messages = array(      // 400 => 'Solicitação Inválida',
        401 => 'Usuário Não Autorizado',
        403 => 'Acesso Negado',
        404 => 'Página Não Encontrada', // 405 => 'Método Não Permitido',
        429 => 'Você está fazendo muitas muitas solicitações.',
        503 => 'Sistema Indisponível para Manutenção',
);

    return isset($http_status_messages[$code]) ? $http_status_messages[$code] : "Status $code"; // Unknown
}


$http_code = http_response_code();

if (isset($_SESSION["browser_noscript"]) && $_SESSION["browser_noscript"] === TRUE ){
    unset($_SESSION["browser_noscript"]);

    $noscript = true;
    $FQDN = $_SERVER["HTTP_HOST"];
    $page_desc = "<p>O site <b>$FQDN</b> precisa de um navegador atualizado com suporte a JavaScript para funcionar corretamente. <br>Utilize os navegadores <a href='https://firefox.com/'>Mozilla Firefox</a> ou <a href='https://chrome.com/'>Google Chrome</a> em suas versões mais recentes. Ou veja como habilitar scripts em seu navegador nos sites <a href='https://www.enablejavascript.io' target='_blank'>enablejavascript.io</a> e <a href='https://www.whatismybrowser.com/pt/detect/is-javascript-enabled' target='_blank'>whatismybrowser.com</a> </p> <br>"; // <a href='/' class='has-text-dark'>Voltar para a Página Inicial</a>
} else {
    $noscript = false;
    $page_desc = get_http_status_message($http_code);
}


$isNoscript = $noscript;
$isCode_blocked = ($http_code == 401 || $http_code == 403);
$isCode_unavailable = ($http_code == 503 || $http_code == 429);

if ($isCode_blocked) {
    echo "<title>$http_code : $page_desc</title>";
    echo "Sua navegação termina aqui: <b>$page_desc</b>";
    exit;
}

$page_title = "Oops!";

require_once 'html/head.php';
require_once 'html/css_Js.php';

?>

    <?= ResourceLoader::returnCSS(); ?>


    <title><?= $meta_title ?></title>
    <!-- <meta name="description" content="<?= $meta_description ?>"> -->

    <meta name="robots" content="nofollow, noindex, noarchive, noodp, noydir">
</head>
<body>


<?php if (!($isNoscript || $isCode_unavailable)) require_once 'html/navbar.php' ?>


<section class="hero is-fullheight">
    <div class="hero-body">
        <div class="container <?= ($isNoscript ? "" : "has-text-centered") ?>">
            <div class="column is-4 is-offset-4">

                <h1 id="HttpStatus_countdown" style="font-size:5rem;" class="as-text-weight-bold">
                    <?= $isNoscript ? $page_title : ($isCode_unavailable ? $page_title : "") ?>
                </h1>

                <hr style="border-bottom: 1px solid black;">
                <h4 class="has-text-black"> <?= $page_desc ?> </h4>


                <?php if (!($isNoscript || $isCode_unavailable)) : ?>
                <p style="font-size:1rem;"> <!-- is-normal -->
                    <a href="/">clique aqui para voltar à página principal</a>
                </p>
                <?php endif; ?>

            </div>
        </div>
    </div>
</section>


<?php if (!($isNoscript || $isCode_unavailable)) ResourceLoader::returnJS() ?>

</body>
</html>
