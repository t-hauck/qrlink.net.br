<?php
require_once 'head.php';

// Limpando e setando novo valor de variável na seção
unset($_SESSION['submitToken']); // Limpando o valor salvo na seção ao abrir a página
$formToken = md5(time());        // o valor é válido apenas enquanto o site estiver aberto
$_SESSION["submitToken"] = $formToken; // POST via Curl = NEGADO


$controller = new LinkAdmin(); // Exibição de Informações na Tela
$DB_name = $controller->obterNomeBanco();
$num_linksSalvos = $controller->contarSalvos();

?>

  <link href="/code?folder=styles&url=reset.css,main.css,fonts.css" rel="stylesheet">
    
</head>
<body>




<main>
    <!-- https://github.com/mebjas/html5-qrcode/tree/master/examples/html5 -->

    <div id="container_qrcode" class="container_qrcode" style="display:none;">
        <div id="qr-reader" style="width: 500px;"></div>
        <div id="qr-reader-results"></div>
    </div>

    <div id="container_link" class="container_link">
        <p class="info_textoLido" id="info_textoLido"></p>
        <div class="box_input">
            <input id="input_linkCompleto" class="input_url" placeholder="Coloque aqui o link" type="url" required>
            <input id="formToken" value="<?= $formToken ?>" type="hidden">
            <?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>
        </div>

        <div class="box_button">
            <button class="btn" id="submitLink_btn" type="submit" href="#">Encurtar Link</button>
            <!-- <button class="btn btn_copiar" id="btn_copiarURL">Copiar</button> .btn_copiar { display: none; } -->
        </div>

        <div class="sysinfo">
          <p><?= $num_linksSalvos ?></p>
          <p>Banco de Dados <?= $DB_name ?></p>
        </div>
    </div> <!-- box -->
</main>


<ul>
    <li class="action" id="">
        <a id="menuBtn_qrcode" href="#!"> <!-- LEITURA DE QR CODE -->
            <img src="/view/img/icons8-qr-code-100.png">
        </a>
    </li>

    <li>
        <a class="sysinfo_btn" href="#!"> <!--  INFORMAÇÕES  -->
            <img src="/view/img/icons8-information-100.png">
        </a>
    </li>

    <li class="action" id="">
        <a id="menuBtn_link" href="#!"> <!-- ENCURTAR LINK -->
            <img src="/view/img/icons8-website-100-add-new.png">
        </a>
    </li>
</ul>



<script type='text/javascript' src='/code?folder=scripts&url=index_menu.js,link.js' async></script>
<script type='text/javascript' src='/code?folder=scripts&url=html5-qrcode.js'></script>
<script type='module' src='/code?folder=scripts&url=qrcode.js'></script>
<noscript async>
    <meta http-equiv="refresh" content="0;url=/noscript">
</noscript>

</body>
</html>
