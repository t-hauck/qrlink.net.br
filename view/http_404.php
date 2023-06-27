<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

?>


    <title>Página Não Encontrada | <?= $meta_title ?></title>
    <meta name="description" content="<?= $meta_description ?>">

    <meta name="robots" content="nofollow, noindex, noarchive, noodp, noydir">
</head>
<body>



<section class="hero is-fullheight-with-navbar">
    <?php require_once 'html/navbar.php'; ?>

    <div class="hero-body">
        <div class="container has-text-centered">
            <div class="column is-4 is-offset-4">

                <h1 id="404_redirectHome" style="font-size:5rem;" class="has-text-weight-bold"></h1> <!-- is-large -->

                <hr style="border-bottom: 1px solid black;">
                <h4 class="title has-text-black">Página Não Encontrada</h4>

                <p class="subtitle" style="font-size:1rem;">A página principal será aberta em alguns segundos.</p> <!-- is-normal -->

            </div>
        </div>
    </div>
</section>


<?= returnJS(); ?>

</body>
</html>