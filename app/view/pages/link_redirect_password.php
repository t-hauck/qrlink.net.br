<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

?>

    <?= ResourceLoader::returnCSS(); ?>
    <link type='text/css' rel='stylesheet' href='/code?folder=styles&url=redirect_pass.css'>

    <title>Link Protegido por Senha | QR-Link</title>
    <meta name="description" content="Insira sua senha para desbloquear este link | QR-Link &mdash; <?= $meta_description ?>">
</head>
<body>





<section class="hero is-fullheight">
    <div class="hero-body">
        <div class="container has-text-centered">
            <div class="column is-4 is-offset-4">
                <h3 class="title has-text-black">Acesso Restrito</h3>
                <hr style="border-bottom: 1px solid black;">

                <p class="subtitle" style="padding-top: 1rem;">Insira sua Senha para Acessar o Link</p>

                <div class="box">
                    <form>
                        <div class="field has-addons">
                            <div class="control">
                                <input class="input is-medium" id="input_linkSenha" type="password" placeholder="**********"
                                        spellcheck="false" autocomplete="off" autocorrect="off" autocapitalize="off"
                                        required>
                            </div>
                            <div class="control">
                                <a class="button is-medium" id="showPassword">
                                    <span class="icon"><i class="fa-solid fa-eye" id="showPassword_icon"></i></span>
                                </a>
                            </div>
                        </div>

                        <button class="button is-block is-info is-medium is-fullwidth" id="submit_checarSenha">
                            Liberar Acesso
                        </button>
                    </form>
                </div>
                <p class="has-text-grey">
                    <a href="/">Página Principal</a> &nbsp;·&nbsp;
                    <a class="sobreSistema">Sobre o QRLink</a>
                    
                    <br>
                    <a id="dark">Mudar Cores</a>
                </p>
            </div>
        </div>
    </div>
</section>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?= ResourceLoader::returnJS(); ?>

</body>
</html>
