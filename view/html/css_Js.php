<?php
require_once "view/info.php";


function returnCSS(){
    $URL = check_URLPath()[1];
    $folder = "styles"; // => gz.php

    if ($URL == "" || $URL == "links") {
        $css = array( "main.css", "sweetalert2.min.css" );
    } else if ($URL == "admin") { // admin não usa o Bulma CSS
        $css = array( "admin.css" );
    } else {
        $css = array();
    }

    array_unshift($css, "reset.css", "bulma.min.css");
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
    $URL = check_URLPath()[1];
    $folder = "scripts"; // => gz.php

    if ($URL == "" || $URL == "links" )
        $js = array( "html5-qrcode.min.js", "qrcode.js" );
    if ($URL == "") // Página Principal == string vazia
        echo "<script type='module' src='/code?folder=$folder&url=qrcode_reader.js'></script>";
    else if ($URL == "links") // Meus Links/Estatisticas
        array_push($js, "localstorage_export.js");
    else if ($URL == "admin") // ADMIN
        $js = array( "link_admin.js" );
    else $js = array();

    array_unshift($js, "sweetalert2.all.min.js", "http.js", "isVisible.js", "localstorage.js", "link.js");
    echo "<script type='text/javascript' src='/code?folder=$folder&url=dark_mode.js' async></script> ";

    foreach ($js as $files) { echo "<script type='text/javascript' src='/code?folder=$folder&url=$files'></script> "; }
    echo "<noscript async><meta http-equiv='refresh' content='0;url=/noscript'></noscript>";
}
