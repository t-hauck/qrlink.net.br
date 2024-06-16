<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "/view/pages/info.php";


function returnCSS(){
    $URL = check_URL()[1];
    $folder = "styles"; // => gz.php

    if ($URL == "" || $URL == "links" || $URL == "sobre" || $URL == "contato") {
        $css = array( "main.css" ); //  "show_hide_table_columns.css"

    } else if ($URL == "sobre") { // => array_unshift
        $css = array();
    } else if ($URL == "admin") { // admin não usa o Bulma CSS
            $css = array("admin.css"); // $css = array("admin.backup.css");
    } else { // redirect_pass.css => redirect_pass.php
        $css = array();
    }

    array_unshift($css, "bulma.min.css", "sweetalert2.min.css", "reset.css");

    foreach ($css as $files) { echo "<link type='text/css' rel='stylesheet' href='/code?folder=$folder&url=$files' defer>"; }
    echo "<link type='text/css' rel='stylesheet preload' as='style' href='/code?folder=$folder&url=fonts.css'>";
    echo "<link type='text/css' rel='stylesheet' href='/code?folder=fontawesome/css&url=all.css' defer>";

    /*  Para enviar os arquivos separados por virgula: transformar array em string
        - isso não é armazenado em cache no navegador conforme gz.php
            $css_string = implode(",", $css);
            echo "<link type='text/css' rel='stylesheet' href='/code?folder=$folder&url=$css_string' async>";
    */
}

function returnJS(){
    $URL = check_URL()[1];
    $folder = "scripts"; // => gz.php

    if ($URL == "" || $URL == "links")
        $js = array( "html5-qrcode.min.js", "qrcode.js" );
    if ($URL == "") // Página Principal == string vazia
        echo "<script type='module' src='/code?folder=$folder&url=qrcode_reader.js'></script>";
    else if ($URL == "links") // Meus Links/Estatisticas
        array_push($js, "localstorage_export.js");
    else if ($URL == "admin") { // ADMIN
        $js = array( "link_admin.js", "qrcode.js" );
        // $js = array( "link_admin_test.js" );
    }
    else $js = array();

    array_unshift($js, "dark_mode.js", "sweetalert2.all.min.js", "http.js", "isVisible.js", "localstorage.js", "link.js");
    foreach ($js as $files) { echo "<script type='text/javascript' src='/code?folder=$folder&url=$files'></script> "; }
    echo "<noscript async><meta http-equiv='refresh' content='0;url=/noscript'></noscript>";
}
