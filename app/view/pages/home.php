<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

?>

    <?= ResourceLoader::returnCSS(); ?>


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

            <div class="column is-8 is-8-desktop mx-auto" id="container_criarLink" style="display:none;">
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

                <div class="column is-7 is-7-desktop mx-auto" id="localData_msg" style="display:none;"></div>

            </div> <!-- container_criarLink -->
        </div> <!-- columns -->

    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container p-4 hover_click paraEstatisticas" style="background-color:#ffce6c; border-radius:20px;">
        <div class="level">
            <div class="level-item has-text-centered">
                <div class="level box transparent">
                    <div class="statcard">
                        <h3 class="statcard-number" id="serverstats_html_records_total"></h3>
                        <span class="statcard-desc">Total de Links</span>
                    </div>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div class="level box transparent">
                    <div class="statcard">
                        <h3 class="statcard-number" id="serverstats_html_access_password_attempts"></h3>
                        <span class="statcard-desc">Tentativas de Acessos a Links Protegidos por Senha</span>
                    </div>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div class="level box transparent">
                    <div class="statcard">
                        <h3 class="statcard-number" id="serverstats_html_records_blocked"></h3>
                        <span class="statcard-desc">Links Suspeitos Bloqueados</span>
                    </div>
                </div>
            </div>
            <div class="level-item has-text-centered">
                <div class="level box transparent">
                    <div class="statcard">
                        <h3 class="statcard-number" id="serverstats_html_access_total"></h3>
                        <span class="statcard-desc">Total de Acessos</span>
                    </div>
                </div>
            </div>
        </div>

        <p class="content is-small has-text-centered">
            Estes são dados gerais do servidor, para visualizar estatísticas dos&nbsp;<span class="is-underlined">seus links</span>, acesse a página&nbsp;<a href="/links">/links</a>.
        </p>
    </div>
</section>

<section class="section" style="padding-top:0;">
    <div class="container">
        <div class="columns is-vcentered">
            <div class="column is-4">
                <article class="box message">
                    <p class="subtitle">Porque QR-Link?</p>
                    <p>
                        QR-Link é um serviço grátis para <strong>encurtar URLs</strong>, criar um <strong>código QR</strong> e ler o conteúdo de um.
                        <!-- Não é necessário nenhum pagamento ou cadastro de conta. -->
                    </p>

                    <p>
                        URLs encurtadas e <strong>QRCodes</strong> que você criar podem ser usadas em <em>qualquer lugar</em>, em redes sociais, blogs, fóruns, emails e mais. Se o link curto for <strong>protegido por senha</strong>, ela será
                        exigida ao acessar o link gerado pelo QR-Link. Saiba mais <u><a href="/sobre">aqui</a></u>.
                    </p>
                </article>
            </div>
            <div class="column is-2">
                <div class="hover_click" id="localData_num_pagelink">
                    <div class="box" id="localData_num"></div>
                </div>
            </div>
        </div>


<!--        <div class="level">
                <article class="box transparent">
                    <p class="title">
                        <span class="icon">
                            <i class="fa-solid fa-handshake-angle"></i>
                        </span> Segurança
                    </p>
                    <div class="content">
                        <p class="subtitle has-text-grey">Bloqueamos URLs que disseminam spam e malware. Entre em contato e solicite o bloqueio de um link através <a href="/contato">desta página</a>.</p>
                    </div>
                </article>

                <article class="box transparent">
                    <p class="title">
                        <span class="icon">
                            <i class="fas fa-chart-line"></i>
                        </span> Estatísticas
                    </p>
                    <div class="content">
                        <p class="subtitle has-text-grey">Veja a data e horário do uĺtimo acesso, o total de acessos dos seus links curtos e mais!</p>
                    </div>
                </article>

            </div>-->
            <!-- level -->

        </div> <!-- container -->
</section>

<footer class="section">
    <div class="container">
        <div class="pb-5 is-flex is-flex-wrap-wrap is-justify-content-between is-align-items-center">
            <div class="mr-auto mb-2"></div>
            <div class="buttons">
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
<?= ResourceLoader::returnJS(); ?>

</body>
</html>
