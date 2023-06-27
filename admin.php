<?php
require_once "LinkController.php";

require_once 'view/html/head.php';
require_once 'view/html/css_Js.php';

$controller      = new LinkController();
$controller_ADM  = new LinkAdmin();

$sql_listAll = $controller_ADM->listarTodos(); // Exbição dos dados salvos
$info_sistema = "[" . $controller->obterNomeBanco() . "-" . PHP_OS . "]" . $controller->contarSalvos();


// Criando variável para redirecionamento com tag HTML <a href=''>
if ( isset($_SERVER['HTTPS']) ) { // isset == localhost
    $PROTOCOL = "https://";
} else $PROTOCOL = "http://";

$FQDN_Redirect = $PROTOCOL . $_SERVER['HTTP_HOST'] . "/";
?>

    <title>Administração | QR-Link</title>
</head>
<body>





<?php if (!$sql_listAll) {
    echo "
        <p class='center' style='padding:50px;'>
            nenhum registro encontrado no banco de dados
        </p>
        ";
    }
?>
<table id="admin_table_filter"> <!-- Pesquisa por Links Salvos -->
    <tr>

        <th><input type="checkbox" id="delete_links_all"></th>
        <th></th>
        
        <th title="URL completa que foi encurtada">
            Link Original
        </th>
        <th class="center" title="Código alfanumérico pequeno">
            Link Curto
        </th>
        
        <th class="center" title="Número total de acessos">
            Acessos
        </th>
        <th class="center" title="Data e horário do último acesso">
            Último Acesso
        </th>
        
        <th class="center" title="Número total de tentativas de acessos mal sucedidas a links curtos protegidos por senha">
            Tentativas
        </th>
        <th class="center" title="Data e horário da última tentativa de acesso mal sucedida a links curtos protegidos por senha">
            Última Tentativa
        </th>

        <th class="center">Data de Cadastro</th>
    </tr>

    




    <?php
        $ordem = 0;
        foreach ($sql_listAll as $Link) : 

            $ordem++
        ?>
    
    <tr>
        <td class="center">
        	<input type="checkbox" class="delete_link" item-shortCode="<?= $Link->short_code ?>">
        </td>

        <td class="center"> <?= $ordem ?> </td>

        <td>
            <a href="<?= $Link->original_url ?>" target="_blank"><?php // Se a URL tiver mais que 100 caracteres adicione "..." no final
                $URL_lenght = 80;
                if (strlen($Link->original_url) > $URL_lenght) echo substr($Link->original_url, 0, $URL_lenght) . "...";
                else echo $Link->original_url
            ?></a>

            <?= $Link->short_code_password === null ? "" : "<img src='/view/img/icons8-password.svg' alt='' title='É necessária uma senha para acessar o link encurtado'>" ?>
        </td>

        <!-- <td class="center">
            <button class="apagar_link table" item-shortCode="<?= $Link->short_code ?>">APAGAR</button>
        </td> -->

        <td class="center">
            <a href="<?= $FQDN_Redirect . $Link->short_code ?>" target="_blank"><?= $Link->short_code ?></a>
        </td>

        <td class="center"> <?= $Link->access ?> </td>
        <td class="center">
            <?= ($Link->last_access) ? $Link->last_access : "-" // Operador Ternário = IF/ELSE ?>
        </td>

        <td class="center">
            <?= ($Link->access_attempts) ? $Link->access_attempts : "-" ?>
        </td>
        <td class="center">
            <?= ($Link->last_access_attempt) ? $Link->last_access_attempt : "-" ?>
        </td>

        <td class="center"> <?= $Link->added_date ?> </td>
    </tr>
    <?php endforeach ?>
</table>




<ul>
    <li>
        <!-- <a class="active" href="/admin">admin</a> -->
        <input placeholder="Pesquise.." id="pesquisa_input"><!-- <a href="#!" id="pesquisa_apagarItens" class="pesquisa_apagarItens"><img src="/view/img/icons8-delete-trash-100_branco.png"></a> --></input>
    </li>
    <li class="sysinfo">
        <p><?= $info_sistema ?></p>
    </li>
    <li class="action">
        <a id="submit_criarLink" href="#!">
            <span class="icon">
                <i class="fa-solid fa-link fa-2x"></i>
            </span>
        </a>
    </li>
    <li class="action">
        <a id="apagar_link_list" href="#!"> <!-- item-shortCode="links" -->            
            <span class="icon">
                <i class="fa-solid fa-trash-can fa-2x"></i>
            </span>
        </a>
    </li>
</ul>



<input id="formToken" value="<?= $_SESSION["submitToken"] ?>" type="hidden">
<?php // validado pelo rotas.php para Segurança, apenas o próprio site pode executar um POST ?>

<?= returnJS(); ?>

</body>
</html>
