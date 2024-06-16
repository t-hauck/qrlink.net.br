<?php
/*
 * Exemplo de uso da classe:
 *
 * ResourceLoader::returnCSS();
 * ResourceLoader::returnJS();
 *
 *
*/

require_once $_SERVER["DOCUMENT_ROOT"] . "/app/helpers/Http.php";

class ResourceLoader {

    private static function getCssFiles($URL) {
        $folder = "styles";
        $baseCss = array("bulma-no-dark-mode.min.css", "sweetalert2.min.css", "reset.css");

        $specificCss = array(
            "/" => array("main.css"),
            "/sobre" => array("main.css"),
            "/contato" => array("main.css"),
            "/links" => array("main.css", "show_hide_table_columns.css"),
            "/admin" => array("admin.css", "show_hide_table_columns.css"),
        );

        $css = array_merge($baseCss, $specificCss[$URL] ?? []);
        return $css;
    }

    private static function getJsFiles($URL) {
        $folder = "scripts";
        $baseJs = array("dark_mode.js", "sweetalert2.all.min.js", "http.js", "isVisible.js", "localstorage.js", "link.js");

        $specificJs = array(
            "/" => array("html5-qrcode.min.js", "qrcode_reader.js", "qrcode.js"),
            "/links" => array("show_hide_table_columns.js", "qrcode.js", "localstorage_export.js"),
            "/admin" => array("show_hide_table_columns.js", "qrcode.js", "link_admin.js"),
        );

        $js = array_merge($baseJs, $specificJs[$URL] ?? []);
        return $js;
    }

    private static function echoLinks($folder, $files, $type) {
        foreach ($files as $file) {
            switch ($type) {
                case "link":
                    echo "<link type='text/css' rel='stylesheet' href='/code?folder=$folder&url=$file' defer>";
                    break;
                case "script":
                    echo "<script type='text/javascript' src='/code?folder=$folder&url=$file'></script> ";
                    break;
            }
        }
    }

    public static function returnCSS() {
        $URL = Http::requestURI();
        $css = self::getCssFiles($URL);

        self::echoLinks("styles", $css, 'link');

        echo "<link type='text/css' rel='stylesheet preload' as='style' href='/code?folder=styles&url=fonts.css'>";
        echo "<link type='text/css' rel='stylesheet' href='/code?folder=fontawesome/css&url=all.css' defer>";
    }

    public static function returnJS() {
        $URL = Http::requestURI();
        $js = self::getJsFiles($URL);

        self::echoLinks("scripts", $js, 'script');
        echo "<noscript async><meta http-equiv='refresh' content='0;url=/_noscript_'></noscript>";
    }
}
