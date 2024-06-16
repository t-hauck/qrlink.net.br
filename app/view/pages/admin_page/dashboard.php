<?php
$PAGES = $_SERVER["DOCUMENT_ROOT"] . "/app/view/pages/";
require_once $PAGES . "html/head.php";
require_once $PAGES . "html/css_Js.php";

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/" . "LinkController.php";

$controller = new LinkController(); // Exibição de Informações na Tela
$DB_name = $controller->obterNomeBanco();
$environment = $controller->systemEnvironment();

// Criando variável para redirecionamento com tag HTML <a href=''>
if ( isset($_SERVER['HTTPS']) ) { // isset == localhost
  $PROTOCOL = "https://";
} else $PROTOCOL = "http://";

$FQDN_Redirect = $PROTOCOL . $_SERVER['HTTP_HOST'] . "/";
?>

    <?= ResourceLoader::returnCSS(); ?>


    <title>Administração | QR-Link</title>
</head>
<body>



<?php require_once $PAGES . "html/navbar.php"; ?>


<main class="main">
  <div class="container">

    <header class="is-clearfix">
      <div class="cats is-pulled-right has-text-right">
        <small id="admin_dash_currentTime"></small>
      </div>
      <div>
        <small>Sistema em <span class="has-text-weight-bold has-text-white"><?= $environment ?></span> <br> com Banco de Dados <span class="has-text-weight-bold has-text-white"><?= $DB_name ?></span></small>
      </div>
      <hr></hr>
    </header>


    <header class="is-clearfix">
      <div class="cats is-pulled-right has-text-right">
        <small>Atualizado a cada<br> <span class="has-text-weight-bold has-text-white">120</span> segundos / 2min</small>
      </div> <!-- <div><h2>Estatísticas</h2><small>de links encurtados</small></div> -->
      <br><br></br>
    </header>

    <div class="level">
      <div class="level-item has-text-centered box">
        <p class="title" id="serverstats_html_records_today"></p>
        <p class="subtitle">Salvos Hoje</p>
      </div>
      <div class="level-item has-text-centered box">
        <p class="title" id="serverstats_html_records_total"></p>
        <p class="subtitle">Total de Links</p>
      </div>
      <div class="level-item has-text-centered box accent">
        <p class="title" id="serverstats_html_records_active"></p>
        <p class="subtitle">Links Ativos</p>
      </div>
      <div class="level-item has-text-centered box transparent">
        <p class="title" id="serverstats_html_records_blocked"></p>
        <p class="subtitle">Links Suspeitos Bloqueados</p>
      </div>
    </div>

    <!-- <header class="is-clearfix">
      <div class="cats is-pulled-right has-text-right">
        <small>Bulma Theme<br>Dashboard<br> <span class="has-text-weight-bold has-text-white">v.0.1</span></small>
      </div>
      <div>
        <h2>Bulma Boxes</h2>
        <small>Dashboard sdff sdfdsfdsf cvfvxgfd</small>
      </div>
      <hr></hr>
    </header> -->
    <div class="level">
      <div class="level-item has-text-centered box">
        <p class="title" id="serverstats_html_access_today"></p>
        <p class="subtitle">Acessados Hoje</p>
      </div>
      <div class="level-item has-text-centered box">
        <p class="title" id="serverstats_html_access_total"></p>
        <p class="subtitle">Total de Acessos</p>
      </div>
      <div class="level-item has-text-centered box">
        <p class="title" id="serverstats_html_access_password_protected"></p>
        <p class="subtitle">Acessos a Links Protegidos</p>
      </div>
      <div class="level-item box">
        <p class="title" id="serverstats_html_access_password_attempts"></p>
        <p class="subtitle">Tentativas de Acessos a<br>Links Protegidos por Senha</p>
      </div>
    </div>
  </div> <!-- container -->



  <div class="container is-fluid">
    <header>
        <hr> <h2 class="has-text-warning is-size-2 is-size-2-mobile has-text-weight-bold">Todos os Links</h2>
    </header>

  <!-- <div style="margin: 20px 5vw 40vh 5vw;"> -->
    <div class="buttons is-align-items-center is-flex"> <!-- style="margin-bottom:0;" -->
      <button class="button is-small is-info is-light" id="table_update" title="Atualizar tabela não considerando o termo atual no campo de busca, outros filtros serão utilizados">
        <span class="icon">
          <i class="fa-solid fa-rotate fa-1x"></i>
        </span>
      </button>

      <a class="button is-small is-info is-light" id="submit_criarLink">
          <span class="icon-text">
              <span class="icon">
                  <i class="fa-solid fa-plus fa-1x"></i>
              </span>
              <span>Cadastrar</span>
          </span>
      </a>
      <a class="button is-small is-info is-light" id="apagar_link_list">
          <span class="icon-text">
              <span class="icon">
                  <i class="fa-regular fa-trash-can fa-1x"></i>
              </span>
              <span>Apagar</span>
          </span>
      </a>

      <div class="dropdown is-hoverable">
        <div class="dropdown-trigger">
          <a class="button is-small is-info is-light" id="bloquear_link_list" aria-haspopup="true" aria-controls="dropdown-menu-block">
              <span class="icon-text">
                  <span class="icon">
                      <i class="fa-solid fa-ban fa-1x"></i>
                  </span>
                  <span>Bloquear/Desbloquear</span>

                  <span class="icon is-small">
                    <i class="fas fa-angle-down" aria-hidden="true"></i>
                  </span>

              </span>
          </a>
        </div>
        <div class="dropdown-menu" id="dropdown-menu-block" role="menu">
          <div class="dropdown-content">
            <div class="dropdown-item">
              <p><strong>Agende</strong> o bloqueio ou desbloqueio de links em uma data específica: </p>
              <br>

              <button class="button is-small is-info is-light" id="bloquear_link_agendar"> <!-- somente HTML ID, falta codigo! -->
                <span class="icon">
                  <i class="fa-solid fa-calendar-days fa-1x"></i>
                </span>
                <span>Definir Data</span>
              </button>
            </div>

            <hr class="dropdown-divider">

            <div class="dropdown-item">
              <p>Apenas a <strong>data</strong> será considerada, a meia-noite o estado do link será alterado.</p>
            </div>
          </div>
        </div>
      </div>


    </div>

    <div class="buttons">
      <div class="field is-grouped">
        <div class="field has-addons has-addons-centered">
          <p class="control">
            <a class="button is-small" id="table_search_btn">
              <i class="fa-solid fa-magnifying-glass"></i>
            </a>
          </p>
          <p class="control" title="Filtrar por um termo específico para pesquisa">
            <input class="input is-small" id="table_search" type="text" placeholder="Pesquise..">
          </p>
          <p class="control">
            <a class="button is-small" id="table_search_ClearInput">
              <i class="fas fa-times"></i>
            </a>
          </p>
        </div>

        <p class="control">
          <div class="select is-small">
            <select id="select_table_rows">
              <option value="25" selected>25</option>
              <option value="50">50</option>
              <option value="100">100</option>
              <option value="250">250</option>
            </select>
          </div>
        </p>
        <p class="control">
          <div class="select is-small">
            <select id="select_table_sort">
              <option value="asc">ASC</option>
              <option value="desc" selected>DESC</option>
            </select>
          </div>
        </p>
        <!-- <p class="control">
          <div class="select is-small">
              <select id="select_table">
                <option value="total">Todos os links</option>
                <option value="blocked">Links bloqueados</option>
                <option value="dbevents">Eventos MySQL</option>
              </select>
          </div>
        </p> -->
      </div> <!-- field is-grouped -->
    </div> <!-- buttons -->

    <div class="tile is-ancestor" style="margin-bottom:40vh;">
      <div class="level-item">
        <article class="tile box">

          <div class="table-container">
            <table class="table is-fullwidth is-narrow" id="table-content-menu">
              <thead id="admin_table_thead"></thead>
              <tbody id="admin_table_tbody"></tbody>
              <tfoot id="admin_table_tfoot"></tfoot>
            </table>

            <ul id="table-menu" class="table_container__menu table_container__menu--hidden"></ul>
          </div>

          <nav class="pagination" role="navigation" aria-label="pagination">
            <a class="pagination-previous" id="table_pagination_prev">Anterior</a>
            <a class="pagination-next" id="table_pagination_next">Próxima</a>
            <ul class="pagination-list" id="pagination_list">

                <!-- <li>
                    <a class="pagination-link" aria-label="Goto page 1">1</a>
                </li>
                <li>
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 45">45</a>
                </li>
                <li>
                    <a class="pagination-link is-current" aria-label="Page 46" aria-current="page">46</a>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 47">47</a>
                </li>
                <li>
                    <span class="pagination-ellipsis">&hellip;</span>
                </li>
                <li>
                    <a class="pagination-link" aria-label="Goto page 86">86</a>
                </li> -->
            </ul>
          </nav>

        </article>
      </div>
    </div>
  </div> <!-- container -->

</main>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?= ResourceLoader::returnJS(); ?>

</body>
</html>
