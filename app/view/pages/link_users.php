<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

require_once 'info.php';
require_once $_SERVER["DOCUMENT_ROOT"] . "/app/LinkController.php";



$controller = new LinkController(); // Exibição de Informações na Tela
$systemCode = $controller->get_SystemShortCode();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'].'/';
$FQDN = $protocol . $domainName;
?>



  <?= ResourceLoader::returnCSS(); ?>

  <title>Estatísticas - Meus Links Curtos</title>
  <meta name="description" content="Veja estatísticas das duas URLs encurtadas, como o número total de acessos e data da última visita. Compartilhe seus links e gere QR Codes!">
</head>
<body>



<?php require_once 'html/navbar.php'; ?>






<section class="section">
    <div class="container">

        <div class="column is-12 is-8-desktop mx-auto">
          <h2 class="is-size-1 is-size-3-mobile">Meus Links</h2>

          <div class="control">  <!-- value="<?= $systemCode ?>" -->
              <input class="input is-medium" id="input_linkEstatisticas" type="text" placeholder="<?= $FQDN . $systemCode ?>">
          </div>
          <div class="field has-addons mt-1 mr-2" style="justify-content: flex-end;">
              <div class="control">
                  <button class="button is-info is-small is-rounded" id="submit_obterEstatisticas" alt="Verificar dados de acesso de um link curto">Verificar Link</button>
              </div>
          </div>

          <div id="input_statsResult"></div>

          <div class="pt-6" style="border-bottom: 1px solid #dee2e6;"></div>
        </div>


        <div class="column is-12 is-12-desktop mx-auto">
            <p class="mb-1">
              Apenas os links encurtados com o seu navegador web atual serão listados aqui. Ao encurtar um link é salvo no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr> o código curto do link criado, os códigos salvos são lidos nesta página e enviado um pedido ao servidor solicitando informações sobre estes links. Se você apagar os dados no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr>, poderá perder essas informações.
            </p>

            <article class="message is-warning hover_click sobreSistema">
                <div class="message-body">Links curtos sem acessos a mais de <b>3 Meses</b> são apagados automaticamente do sistema</div>
            </article>

            <div class="mb-3" id="local_buttons" style="display:none; align-items:center;">
                <button class="button is-light mb-2" id="export" alt="Exportar links para um arquivo">Exportar Dados</button>
                <br>

                <label class="checkbox">
                  <input type="checkbox" id="local_autoupdate"> Atualizar automaticamente
                  <small id="local_autoupdate_timer"></small>
                </label>
            </div>

             <table class="table is-fullwidth is-narrow" id="table-content-menu">
              <thead id="table_thead"></thead> <!-- Tabela criada para exibição dos dados no site para o usuário -->
              <tbody id="table_tbody"></tbody>
              <tfoot id="table_tfoot"></tfoot>
            </table>

            <table class="table is-fullwidth" id="export_table" style="display:none;">
              <thead> <!-- Tabela criada apenas para exportação dos dados para CSV -->
                <tr>
                  <th>Link Original</th>
                  <th>Link Curto</th>
                  <th>Proteção</th>
                  <th>Acessos</th>
                  <th>Último Acesso</th>
                  <th></th>
                  <th>Tentativas</th>
                  <th>Última Tentativa</th>
                </tr>
              </thead>
              <tbody id="table_tbody_export"></tbody>
            </table>

            <ul id="table-menu" class="table_container__menu table_container__menu--hidden"></ul>
        </div>
      
      </div>
    </section>




<footer class="section">
    <div class="container">
        <div class="pb-5 is-flex is-flex-wrap-wrap is-justify-content-between is-align-items-center" id="tablefooter">
            <div class="mr-auto mb-2 "></div>
            <div class="is-flex-tablet"></div>
        </div>
    </div>
</footer>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?= ResourceLoader::returnJS(); ?>

</body>
</html>
