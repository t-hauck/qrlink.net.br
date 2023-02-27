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

  <link href="/view/css/reset.css" rel="stylesheet">
  <link href="/view/css/style.css" rel="stylesheet">
  <link href="/view/css/fonts.css" rel="stylesheet">    
    
</head>
<body>




<main>
    <div class="box">
        <!-- <div id="qr-reader" style="width: 500px;"></div>
        <div id="qr-reader-results"></div> -->
        <!-- https://github.com/mebjas/html5-qrcode/tree/master/examples/html5 -->

        <p class="info_textoLido" id="info_textoLido"></p>
        <div class="box_input">
            <input id="input_linkCompleto" class="input_url" placeholder="Coloque aqui o link" type="url" required>
            <input id="formToken" value="<?= $formToken ?>" type="hidden">
            <?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>
        </div>

        <div class="box_button">
            <button class="btn" id="btn_submit" type="submit" href="#">Encurtar Link</button>
            <!-- <button class="btn btn_copiar" id="btn_copiarURL">Copiar</button> .btn_copiar { display: none; } -->
        </div>
    </div> <!-- box -->

    <div class="info_sistema">
          <p><?= $num_linksSalvos ?></p>
          <p>Banco de Dados <?= $DB_name ?></p>
    </div>
</main>





<script src="view/js/link.js" async></script>
<script src="view/js/qrcode.js" type="module"></script>
<script src="view/js/html5-qrcode.min.js"></script>

</body>
</html>
