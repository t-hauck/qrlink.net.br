<?php
// https://codepen.io/netzzwerg/pen/aPBGWq

require_once 'view/html/head.php';
require_once 'view/html/css_Js.php';

?>


    <title>Administração | QR-Link</title>
</head>
<body>



<?php require_once 'view/html/navbar.php'; ?>


<div class="wrapper">

  <div class="">

    <aside class="sidebar">
      <nav class="menu">
        <div class="menu-category">
          <header class="category-header">Dashboard</header>
          <ul class="menu-list">
            <li><a>Static</a></li>
            <li><a>Streaming</a></li>
          </ul>
        </div>
        <div class="menu-category">
          <header class="category-header">Elements</header>
          <ul class="menu-list">
            <li><a>Box</a></li>
            <li><a>Button</a></li>
            <li><a>Content</a></li>
            <li><a>Delete</a></li>
            <li><a>Icon</a></li>
            <li><a>Image</a></li>
            <li><a>Notification</a></li>
            <li><a>Progress bars</a></li>
            <li><a>Table</a></li>
            <li><a>Tag</a></li>
            <li><a>Title</a></li>
          </ul>
        </div>
        <div class="menu-category">
          <header class="category-header">Components</header>
          <ul class="menu-list">
            <li><a>Panels</a></li>
            <li><a>Breadcrumb</a></li>
            <li><a>Card</a></li>
            <li><a>Dropdown</a></li>
            <li><a>Menu</a></li>
            <li><a>Message</a></li>
            <li><a>Modal</a></li>
            <li><a>Navbar</a></li>
            <li><a>Pagination</a></li>
            <li><a>Panel</a></li>
            <li><a>Tabs</a></li>
          </ul>
        </div>
        <div class="menu-category">
          <header class="category-header">Pages</header>
          <ul class="menu-list">
            <li><a>Login</a></li>
            <li><a>Logout</a></li>
            <li><a>Page not found</a></li>
          </ul>
        </div>
      </nav>
    </aside>

    <main class="main">

      <header class="is-clearfix">
        <div class="cats is-pulled-right has-text-right">
          <small>Bulma Theme<br>Dashboard<br> <span class="has-text-weight-bold has-text-white">v.0.1</span></small>
        </div>
        <div>
          <h2>Dashboard</h2>
          <small>Dashboard sdff sdfdsfdsf cvfvxgfd</small>
        </div>
        <hr></hr>
      </header>
      <div class="tile is-ancestor">
      <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_records_today"></p>
            <p class="subtitle">Salvos Hoje</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_records_total"></p>
            <p class="subtitle">Total de Links</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box accent">
            <p class="title"
              id="serverstats_html_records_active"></p>
            <p class="subtitle">Links Ativos</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box transparent">
            <p class="title"
              id="serverstats_html_records_blocked"></p>
            <p class="subtitle">Links Bloqueados</p>
          </article>
        </div>
      </div>

      <header class="is-clearfix">
        <div class="cats is-pulled-right has-text-right">
          <small>Bulma Theme<br>Dashboard<br> <span class="has-text-weight-bold has-text-white">v.0.1</span></small>
        </div>
        <div>
          <h2>Bulma Boxes</h2>
          <small>Dashboard sdff sdfdsfdsf cvfvxgfd</small>
        </div>
        <hr></hr>
      </header>
      <div class="tile is-ancestor">
        <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_access_today"></p>
            <p class="subtitle">Acessados Hoje</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_access_total"></p>
            <p class="subtitle">Total de Acessos</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_access_password_protected"></p>
            <p class="subtitle">Acessos a Links Protegidos</p>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <p class="title"
              id="serverstats_html_access_password_attempts"></p>
            <p class="subtitle">Tentativas de Acessos a Links Protegidos</p>
          </article>
        </div>
      </div>

      <header class="is-clearfix">
        <div class="cats is-pulled-right has-text-right">
          <small>Dashboard Theme<br>Bulma Elements<br> <span class="has-text-weight-bold has-text-white">v.7.2</span></small>
        </div>
        <div>
          <h2>Bulma Elements</h2>
          <small>Bulma Elements are essential interface elements<br/> that only require a single CSS class.</small>
        </div>
        <hr></hr>
      </header>
      <div class="tile is-ancestor">
        <div class="tile is-parent">
          <article class="tile is-child box">
            <div>
              <nav class="breadcrumb" aria-label="breadcrumbs">
                <ul>
                  <li><a href="#">Bulma</a></li>
                  <li><a href="#">Components</a></li>
                  <li class="is-active"><a href="#" aria-current="page">Breadcrumb</a></li>
                </ul>
              </nav>
              <div class="content">
                <p>A simple breadcrumb component to improve your navigation experience.</p>
              </div>
            </div>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <div class="tabs is-centered">
              <ul>
                <li><a>Elements</a></li>
                <li class="is-active"><a>Components</a></li>
                <li><a>Widgets</a></li>
                <li><a>Tiles</a></li>
              </ul>
            </div>
            <div class="content">
              <p>Simple responsive horizontal navigation tabs, with different styles.</p>
            </div>
          </article>
        </div>
      </div>
      <div class="tile is-ancestor">

        <div class="tile is-parent">
          <article class="tile is-child box">
            <nav class="pagination" role="navigation" aria-label="pagination">
              <a class="pagination-previous">Previous</a>
              <a class="pagination-next">Next page</a>
              <ul class="pagination-list">
                <li>
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
                </li>
              </ul>
            </nav>
          </article>
        </div>
      </div>

      <header class="is-clearfix">
        <div class="cats is-pulled-right has-text-right">
          <small>Bulma Theme<br>Dashboard<br> <span class="has-text-weight-bold has-text-white">v.0.1</span></small>
        </div>
        <div>
          <h2>Custom Stat Cards</h2>
          <small>Custom stat cards to easily display large numbers, <br/>great for any kind of simple metrics and dashboard content.</small>
        </div>
        <hr></hr>
      </header>
      <div class="tile is-ancestor">
        <div class="tile is-parent">
          <article class="tile is-child box transparent">
            <div class="statcard">
              <h3 class="statcard-number">28,745</h3>
              <span class="statcard-desc">Page views</span>
            </div>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box transparent">
            <div class="statcard">
              <h3 class="statcard-number">
                72,134
                <small class="delta-indicator delta-positive">5%</small>
              </h3>
              <span class="statcard-desc">Page views</span>
            </div>
          </article>
        </div>
        <div class="tile is-parent">
          <article class="tile is-child box">
            <div class="statcard">
              <h3 class="statcard-number">12,938</h3>
              <span class="statcard-desc">Page views</span>
            </div>
          </article>
        </div>

        <div class="tile is-parent">
          <article class="tile is-child box accent">
            <div class="statcard">
              <h3 class="statcard-number">12,938</h3>
              <span class="statcard-desc">Page views</span>
            </div>
          </article>
        </div>
      </div>

    </main>
  </div>
</div>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>

<?= returnJS(); ?>

</body>
</html>