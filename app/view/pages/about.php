<?php
require_once 'html/head.php';
require_once 'html/css_Js.php';

require_once 'info.php';

?>

    <?= ResourceLoader::returnCSS(); ?>


    <title>Sobre</title>
    <meta name="description" content="<?= $meta_description ?>">
</head>
<body>



<?php require_once 'html/navbar.php'; ?>


<section class="section">
    <div class="container p-4">
        <div class="columns is-multiline">
            <div class="column is-12 is-12-desktop">

                <div class="mb-5">
                    <h1 class="is-size-2 is-size-3-mobile">O que é o QR-Link?</h1>
                    <p>QR-Link é um sistema gratuito com ferramentas para encurtar URLs, criar e escanear um código QR.</p>
                    <p>
                        Bloqueamos URLs que disseminam spam e malware, uma URL bloqueada não poderá ser acessada e cadastrada novamente no sistema.
                        <br> Solicite o bloqueio de um link através <a href="/contato">desta página</a>.
                    </p>

                    <br>
                    <p>
                        O sistema não possui login e cadastro de conta. O QR-Link foi desenvolvido para coletar o mínimo de dados possível, de forma a manter o anonimato e privacidade dos seus usuários.
                        Alguns dados são coletados automaticamente, e estes dados são:
                    </p>
                    <p>
                        – Data e horário dos acessos as páginas do site e links curtos
                        <br> – Seu endereço de protocolo de Internet (IP)
                        <br> – Demais informações sobre links curtos, disponíveis publicamente na página de <a href="/links">estatísticas</a> (/links)
                        <!-- <br> – Características do dispositivo e navegador utilizado -->
                    </p>
                </div>

                <div class="mb-5">
                    <h1 class="is-size-2 is-size-3-mobile">Ferramentas</h1>

                    <div class="mb-2">
                        <h1 class="is-size-4 is-size-4-mobile has-text-weight-bold">Encurtamento de Links</h1>
                        <p>
                            Ao encurtar um link, se for informada uma senha para os acessos ao link curto ela será criptografada antes de ser salva no banco de dados.
                            <br> A senhas são criptografadas usando o algoritmo bcrypt. O administrador não pode pode ver a senha inserida pelo usuário.
                            <!-- <br> A ferramenta de encurtamento de links utiliza o banco de dados -->
                        </p>

                        <h1 class="is-size-6 is-size-5-mobile has-text-weight-bold">Contador de Acessos</h1>
                        <p>
                            Você pode ver as URLs que encurtou clicando no link « Meus Links » em cima da página à direita.
                            <br> Ao encurtar uma URL será salvo no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr> o código curto que foi gerado para o link.
                            <br> Se houverem dados salvos no <abbr title="<?= $sobre_LocalStorage ?>" class="info_Touch">LocalStorage</abbr> no seu navegador web atual, quando acessar a página "Meus Links" será enviada uma solicitação ao servidor com os códigos dos links que você encurtou e o servidor responderá com todos os dados sobre os links, menos a data de cadastro no sistema.
                            <br> A página do Contador de Acessos possui um botão para exportação dos links que você salvou, com seu navegador web atual, você poderá salvar em um arquivo no formato CSV dados dos seus links como data da última visita e quantidade de acessos.
                            <br> Esta página possui um campo de texto, utilize-o para buscar informações de um link que não aparece na tabela.
                        </p>
                        <article class="message is-danger">
                            <div class="message-body">Todos os links encurtados são apagados automaticamente do banco de dados após <b>3 Meses</b> sem nenhum acesso</div>
                        </article>
                    </div>

                    <div class="mb-2">
                        <h1 class="is-size-4 is-size-4-mobile has-text-weight-bold">Criação de QRCode</h1>
                        <p class="mb-1">
                            Você pode criar um QRCode com qualquer texto, de qualquer tamanho.
                            <br> Se o QRCode for um link para um site, você pode adicionar uma <span class="is-underlined">senha</span> para ele. Para adicionar uma senha ao QRCode utilize a ferramenta de <a class="criarLink">encurtamento de links</a> disponível na página principal: encurte o link com a senha desejada e crie um código QR com o link curto que o sistema irá retornar.
                        </p>
                        <p>
                            Na criação de códigos QR é utilizado um serviço externo: Google Charts
                            <br> Ao criar um código QR será enviado apenas o texto informado para o Google Charts, e o QRCode retornado pelo Google Charts será exibido na tela para o usuário.
                            <br> Para informações sobre privacidade, leia a política de privacidade do Google em 
                            
                            <a href="https://policies.google.com/privacy" target="_blank" rel="nofollow">policies.google.com/privacy</a>
                        </p>
                    </div>

                    <div>
                        <h1 class="is-size-4 is-size-4-mobile has-text-weight-bold">Escaneamento de QRCode</h1>
                        <p class="mb-1">
                            Para ler um código QR você pode selecionar uma imagem salva em seu dispositivo, ou  utilizar a câmera do seu aparelho e apontá-la para o código.
                            <br>
                            Ao escanear o código usando sua câmera será solicitada permissão para o sistema QR-Link ler o código usando a câmera do seu dispositivo.
                        </p>
                        <p>
                            O escaneamento é feito completamente no seu navegador web, nenhum dado é salvo no servidor que hospeda este site ou enviado para serviços externos.
                            <br> Para isso, é utilizada a biblioteca "html5-qrcode" que trabalha quando o site está aberto no seu navegador web.
                            <br> O código-fonte desta biblioteca está disponível em <a href="https://github.com/mebjas/html5-qrcode" target="_blank" rel="external">github.com/mebjas/html5-qrcode</a>
                        </p>
                    </div>

                </div>

            </div>
        </div>
</section>



<footer class="section">
    <div class="container">
        <div class="pb-5 is-flex is-flex-wrap-wrap is-justify-content-between is-align-items-center">
            <div id="localData_msg" style="display:none;"></div>
            <div class="mr-auto mb-2"></div>
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
