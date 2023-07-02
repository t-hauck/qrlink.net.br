<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';



$controller = new LinkController(); // Exibição de Informações na Tela
$num_linksSalvos = $controller->contarSalvos();
$num_linksSalvosHoje = $controller->contarSalvos_hoje();

?>


    <title><?= $meta_title ?></title>
    <meta name="description" content="<?= $meta_description ?>">
</head>
<body>



<?php require_once 'html/navbar.php'; ?>


<section class="section">
    <div class="container">
        <div class="mb-6 pb-3 columns is-multiline">
            <div class="column is-5 is-5-desktop mx-auto" id="container_qrScan" style="display:none;">
                <div id="qr-reader"></div>
            </div>

            <div class="column is-8 is-8-desktop mx-auto" id="container_criarQRCode" style="display:none;">
                <div class="field has-addons has-addons-centered">
                    <div class="control" style="width:100%;">
                        <input class="input is-medium" id="input_qrOriginal" type="url" placeholder="Insira o texto aqui" autocomplete="off" autofocus required>
                    </div>
                    <div class="control">
                        <button class="button is-info is-medium is-rounded" id="submit_criarQRCode" alt="Criar um QRCode com um texto">Criar QRCode</button>
                    </div>
                </div>
                <!-- <div class="field has-addons mt-1 mr-2" style="justify-content: flex-end;"><div class="control"><button class="button is-info is-small is-rounded" id="submit_criarQRCode">Criar QRCode</button></div></div> -->
            </div> <!-- container_criarQRCode -->

            <div class="column is-8 is-8-desktop mx-auto" id="container_criarLink">
                <div class="field has-addons has-addons-centered">
                    <div class="control" style="width:100%;">
                        <input class="input is-medium" id="input_linkCompleto" type="url" placeholder="Coloque aqui o link" autocomplete="off" autofocus required>
                    </div>
                    <div class="control">
                        <button class="button is-medium is-rounded is-info" id="submit_criarLink" alt="Encurtar um link grande">Encurtar Link</button>
                    </div>
                </div>
                <div class="field is-horizontal">
                    <div class="field-label is-small">
                        <label class="label">Adicione uma senha para o link curto</label>
                    </div>
                    <div class="field-body">
                        <div class="field">
                            <p class="control">
                                <input class="input is-small" id="input_linkSenha" type="password" placeholder="Senha / Palavra-Passe"
                                        spellcheck="false" autocomplete="off" autocorrect="off" autocapitalize="off">
                            </p>
                        </div>
                    </div>
                </div>

                <div class="column is-7 is-7-desktop mx-auto" id="msgLocalData" style="display:none;"></div>

            </div> <!-- container_criarLink -->
        </div> <!-- columns -->
      
        <div class="columns is-multiline">
            <div class="column is-12 is-4-desktop">
                <div class="mb-6 is-flex ferramentas_hover criarQRCode">
                    <!-- <figure class="image is-48x48">
                        <img class="image" src="/view/img/qr-code-scan.svg" alt="">
                    </figure> -->
                    <span class="icon">
                        <i class="fa-solid fa-qrcode fa-2x"></i>
                    </span>

                    <div class="ml-3">
                        <h1 class="is-size-4 has-text-weight-bold mb-2">Crie um Novo QRCode</h1>
                        <p class="subtitle has-text-grey">Compartilhe um texto de qualquer tamanho usando uma imagem</p>
                    </div>
                </div>

                <div class="mb-6 is-flex ferramentas_hover qrScan">
                    <!-- <figure class="image is-48x48">
                        <img class="image" src="/view/img/icons8-phone-camera-100.png" alt="">
                    </figure> -->
                    <span class="icon">
                        <!-- <i class="fa-solid fa-mobile-screen-button fa-2x"></i> -->
                        <i class="fas fa-mobile-alt fa-2x"></i>
                    </span>

                    <div class="ml-3">
                        <h1 class="is-size-4 has-text-weight-bold mb-2">Escaneie um QRCode</h1>
                        <p class="subtitle has-text-grey">Leia um código QR que foi compartilhado com você</p>
                    </div>
                </div>
            </div>

            <div class="column is-4 is-block-desktop is-hidden-touch">
                <div class="column is-mobile has-text-centered">
                    <figure class="image is-220x220 is-inline-block"> <!-- style="border: 1px solid #485fc7;" -->
                    <?php
                        $FQDN = $_SERVER['HTTP_HOST'];
                        if  ($FQDN === "qrlink.net.br"){ echo "<img src='view/img/qrcode_QRLink.png'>"; }
                        else {
                            echo "<img src='https://chart.apis.google.com/chart?chs=500x500&cht=qr&chl=$FQDN&choe=UTF-8&chld=H'>";
                        }
                            echo "<figcaption class='has-text-centered'>Link para a página principal deste site, em código QR.</figcaption>";
                    ?>
                    </figure>
                </div>
            </div>

            <div class="column is-12 is-4-desktop">
                <div class="mb-6 is-flex ferramentas_hover criarLink">
                    <!-- <figure class="image is-48x48">
                        <img class="image" src="/view/img/share-fill.svg" alt="">
                    </figure> -->
                    <span class="icon">
                        <i class="fas fa-link fa-2x"></i> <!-- fa-4x mb-4 -->
                    </span>

                    <div class="ml-3">
                        <h1 class="is-size-4 has-text-weight-bold mb-2">Encurtador de Links</h1>
                        <p class="subtitle has-text-grey">Diminua o tamanho de qualquer link e compartilhe URLs menores</p>
                    </div>
                </div>
                <div class="mb-6 is-flex ferramentas_hover paraEstatisticas">
                    <span class="icon">
                        <i class="fas fa-chart-line fa-2x"></i>
                    </span>

                    <div class="ml-3">
                        <h1 class="is-size-4 has-text-weight-bold mb-2">Estatísticas de Acessos</h1>
                        <p class="subtitle has-text-grey">Veja o total de acessos da sua URL encurtada</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="columns is-vcentered pt-4">

            <div class="column is-half">
                <div class="notification is-link">
                    QR-Link é um serviço grátis para <strong>encurtar URLs</strong>, criar um <strong>código QR</strong> e ler o conteúdo de um. Não é necessário nenhum pagamento ou cadastro de conta. <br>
                    As URLs encurtadas e <strong>QRCodes</strong> que você criar podem ser usadas em <em>qualquer lugar</em>, em redes sociais, blogs, fóruns, emails e mais. Saiba mais <u><a href="/sobre">aqui</a></u>.
                </div>
            </div>

        <!-- </div>
        <div class="columns is-centered mb-4"> -->

            <div class="column is-one-quarter">
                <div class="mb-6 is-flex">
                    <span class="icon">
                        <i class="fa-sharp fa-solid fa-shield fa-2x"></i>
                    </span>

                    <div class="ml-3">
                        <h2 class="is-size-4 has-text-weight-bold mb-2">Seguro</h2>
                        <p class="subtitle has-text-grey">Usamos o protocolo https com criptografia de dados</p>
                    </div>
                </div>
            </div>
            <!-- <div class="column is-one-quarter">
                <div class="mb-6 is-flex">
                    <span>
                        <img class="image" src="/view/img/icons8-helping-hand-100.png" alt="" width="48px">
                    </span>
                    <div class="ml-3">
                        <h4 class="is-size-4 has-text-weight-bold mb-2">Confiável</h4>
                        <p class="subtitle has-text-grey">Apagamos links curtos que disseminam spam e malware</p>
                    </div>
                </div>
            </div> -->
            <div class="column is-one-quarter">
                <div class="mb-6 is-flex">
                    <span class="icon">
                        <i class="fa-solid fa-user-secret fa-2x"></i>
                    </span>

                    <div class="ml-3">
                        <h2 class="is-size-4 has-text-weight-bold mb-2">Gratuito</h2>
                        <p class="subtitle has-text-grey">Sem cadastro de conta, e sem rastreamento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container p-4 paraEstatisticas">
        <!-- style="background-color:#ffce6c; border-radius:5px;"> -->
        <div class="is-vcentered columns is-multiline">
            <div class="column is-4 is-4-desktop mb-4">
                <h3 class="is-size-2 is-size-3-mobile has-text-weight-bold">Estatísticas</h3>
                <p>Clique aqui e verifique o número total de acessos do seu link curto e a data e horário do último acesso que teve</p>
            </div>
            <div class="column is-3 ml-auto">
                <div class="mx-auto box p-6 has-background-warning has-text-centered"> <!-- has-background-warning -->
                <!-- <h3 class="is-size-2 mb-2 has-text-weight-bold"><?= $num_linksSalvos ?></h3> -->
                    <h3><?= $num_linksSalvos ?></h3>
                </div>
            </div>
            <div class="column is-3 ml-auto">
                <div class="mx-auto box p-6 has-background-warning has-text-centered">
                    <h3><?= $num_linksSalvosHoje ?></h3>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="section">
    <div class="container">
        <div class="pb-5 is-flex is-flex-wrap-wrap is-justify-content-between is-align-items-center">
            <div class="mr-auto mb-2"></div>
            <div>
                <ul class="is-flex is-flex-wrap-wrap is-align-items-center is-justify-content-center">
                    <li class="mr-4"><a class="button is-white paraEstatisticas">Meus Links</a></li>
                    <li class="mr-4"><a class="button is-white criarLink">Encurtar Link</a></li>
                    <li class="mr-4"><a class="button is-white criarQRCode">Criar QRCode</a></li>
                    <li class="mr-4"><a class="button is-white qrScan">Escanear QRCode</a></li>
                </ul>
            </div>
        </div>
        <div class="pt-5 linha_footer"></div>
    </div>
    <div class="container">
        <div class="is-flex-tablet is-justify-content-between is-align-items-center">
            <p>&copy; <?= date("Y"); ?> QR-Link</p>
        </div>
    </div>
</footer>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>

<?= returnJS(); ?>

</body>
</html>
