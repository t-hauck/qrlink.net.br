<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

require_once 'info.php';



$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'].'/';
$FQDN = $protocol . $domainName;
?>

    <?= ResourceLoader::returnCSS(); ?>


    <title>Contato - Reporte um Link Malicioso</title>
    <meta name="description" content="<?= $meta_description ?>">
</head>
<body>



<?php require_once 'html/navbar.php'; ?>






<section class="section">
    <div class="container">

        <div class="column is-12 is-8-desktop mx-auto">
          <h2 class="is-size-3 is-size-3-mobile">Reporte um Link Curto Malicioso</h2>


          Use o formulário abaixo para reportar URLs suspeitas de spam, phishing, malware ou outras atividades ilegais. URLs curtas que redirecionam para páginas maliciosas ou download de arquivos perigosos são bloqueadas.

          <div class="control">
              <input class="input is-medium" id="input_linkEstatisticas" type="text" placeholder="">
          </div>

          <div class="field has-addons mt-1 mr-2" style="justify-content: flex-end;">
              <div class="control">
                  <button class="button is-info is-small is-rounded" id="submit_obterEstatisticas" alt="Enviar mensagem">
                    Enviar</button>
              </div>
          </div>

        </div>
      
    </div>
</section>




<footer class="section">
    <div class="container">
        <div class="pb-5 is-flex is-flex-wrap-wrap is-justify-content-between is-align-items-center">
            <div class="mr-auto mb-2"></div>

            <div class="is-flex-tablet is-justify-content-between is-align-items-center">
              <p id="show_UserTimeZone"></p>
            </div>

        </div>
    </div>
</footer>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?= ResourceLoader::returnJS(); ?>

</body>
</html>
