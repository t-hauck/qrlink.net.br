<?php
require_once "LinkController.php";
require_once 'view/head.php';

// Limpando e setando novo valor de variável na seção
unset($_SESSION['submitToken']); // Limpando o valor salvo na seção ao abrir a página
$formToken_apagar = md5(time()); // o valor é válido apenas enquanto o site estiver aberto
$_SESSION["submitToken"] = $formToken_apagar; // POST via Curl = NEGADO

// Criando variável para redirecionamento com tag HTML <a href=''>
if ( isset($_SERVER['HTTPS']) ) { // isset == localhost
    $PROTOCOL = "https://";
} else $PROTOCOL = "http://";

$FQDN_Redirect = $PROTOCOL . $_SERVER['HTTP_HOST'] . "/";



$controller = new LinkAdmin();
$sql_listAll = $controller->listarTodos(); // Exbição dos dados salvos
$info_sistema = "[" . $controller->obterNomeBanco() . "-" . PHP_OS . "]" . $controller->contarSalvos();
?>

  <link href="/code?folder=styles&url=reset.css,admin.css,fonts.css" rel="stylesheet">
    
</head>
<body>





<?php if (!$sql_listAll) {
    echo "
        <p class='center' style='padding:50px;'>
            nenhum registro encontrado no banco de dados
        </p>
        ";
    }
?>
<input id="formToken" value="<?= $formToken_apagar ?>" type="hidden">
<table id="filtro_tabela"> <!-- Pesquisa por Links Salvos -->
    <tr>
        <th class="center">id</th>
        <th>Link Original</th>
        <th class="center">
            <div id="pesquisa_container" class="pesquisa_container">
                <input placeholder="Pesquise.." id="pesquisa_input">
                <a href="#!" id="pesquisa_apagarItens" class="pesquisa_apagarItens">
                    <img src="/view/img/icons8-delete-trash-100_branco.png"> <!-- APAGAR -->
                </a>
            </div>
        </th>
        <th class="center">Link Curto</th>
        <th class="center">Acessos</th>
        <th class="center">Último Acesso</th>
        <th class="center">Data de Cadastro</th>
    </tr>



    <?php foreach ($sql_listAll as $Link) : ?>
    <tr>
        <td class="center"> <?= $Link->id ?> </td>

        <td>
            <a href="<?= $Link->url ?>" target="_blank"><?php // Se a URL tiver mais que 100 caracteres adicione "..." no final
                $URL_lenght = 80;
                if (strlen($Link->url) > $URL_lenght) echo substr($Link->url, 0, $URL_lenght) . "...";
                else echo $Link->url
            ?></a>
        </td>

        <td class="center">
            <button class="apagar_link table" item-shortCode="<?= $Link->short_code ?>">APAGAR</button>
        </td>

        <td class="center">
            <a href="<?= $FQDN_Redirect . $Link->short_code ?>" target="_blank"><?= $Link->short_code ?></a>
        </td>

        <td class="center"> <?= $Link->hits ?> </td>
        <td class="center">
            <?= ($Link->last_acess) ? $Link->last_acess : "-" // Operador Ternário = IF/ELSE ?>
        </td>
        <td class="center"> <?= $Link->added_date ?> </td>
    </tr>
    <?php endforeach ?>
</table>




<ul>
    <li>
        <a class="active" href="/qrlink/admin">admin</a>
    </li>
    <li class="sysinfo">
        <p><?= $info_sistema ?></p>
    </li>
    <li class="action">
        <a id="submitLink_btn" href="#!">
            <img src="/view/img/icons8-add-new-100.png">           <!-- ADICIONAR -->
        </a>
    </li>
    <li class="action">
        <a class="apagar_link" href="#!" item-shortCode="links">
            <img src="/view/img/icons8-delete-trash-100.png">      <!-- APAGAR -->
        </a>
    </li>
</ul>


<script type='text/javascript' src='/code?folder=scripts&url=link.js' async></script>
<noscript async>
    <meta http-equiv="refresh" content="0;url=/noscript">
</noscript>

</body>
</html>
