<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

require_once 'info.php';



$controller = new LinkController(); // Exibição de Informações na Tela
$systemCode = $controller->get_SystemShortCode();

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$domainName = $_SERVER['HTTP_HOST'].'/';
$FQDN = $protocol . $domainName;
?>


  <title>Meus Links</title>
  <meta name="description" content="Veja estatísticas dos seus links curtos: número de acessos e data da última visita. Você pode salvar seus links em um arquivo e compartilhar um QRCode.">
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
              Apenas os links encurtados com o seu navegador web atual serão listados aqui. As informações abaixo são armazenadas no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr>, se apagar os seus dados no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr>, poderá perder essa informação.
            </p>
            <p class="mb-2">Não serão listados links que já não estão disponíveis, links que não tem nenhum acesso a mais de <u>3 Meses</u> são apagados automaticamente do sistema.</span>
            </p>


            <div class="mb-3" id="local_buttons" style="display:none; align-items:center;">
                <button class="button is-light mb-2" id="export" alt="Exportar links para um arquivo">Exportar Dados</button>
                <br>

                <label class="checkbox">
                  <input type="checkbox" id="local_autoupdate"> Atualizar automaticamente
                  <small id="local_autoupdate_timer"></small>
                </label>
            </div>

            <table class="table is-fullwidth" id="qrcode_click">
              <thead> <!-- Tabela criada para exibição dos dados no site para o usuário -->
                <tr>
                  <th><abbr title="Ordem do link na tabela">N.</abbr></th>
                  <th><abbr title="URL completa que foi encurtada">Link Original</abbr></th>
                  <th></th> <!-- SENHA/Icones = nome da coluna em branco -->
                  <th><abbr title="Código alfanumérico pequeno">Código</abbr></th>
                  <th><abbr title="Número total de acessos">Acessos</abbr></th>
                  <th><abbr title="Data e horário do último acesso">Último Acesso</abbr></th>
                  <th><abbr title="Número total de tentativas de acessos mal sucedidas a links curtos protegidos por senha">Tentativas</abbr></th>
                  <th><abbr title="Data e horário da última tentativa de acesso mal sucedida a links curtos protegidos por senha">Última Tentativa</abbr></th>
                </tr>
              </thead>
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
<?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>

<?= returnJS(); ?>

</body>
</html>
