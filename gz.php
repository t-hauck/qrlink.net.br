<?php
/**
 * -----------------------------------------------------------------------------------------
 * Fonte `https://github.com/leejefon/JavaScript-CSS-Minifier-in-PHP`
 * -----------------------------------------------------------------------------------------
**/
$_FQDN = "https://{$_SERVER['HTTP_HOST']}";
$FOLDER = $_SERVER["DOCUMENT_ROOT"] . "/app/view/assets/";



if ( isset($_GET["folder"]) && isset($_GET["url"]) && !empty($_GET["folder"]) && !empty($_GET["url"]) ){
    $urls = escapeshellcmd( $_GET["url"] );
    $folder = escapeshellcmd( $_GET["folder"] );

    // mascaramento/Ocultando as pastas dos arquivos para exibição no HTML
    if (strpos($folder, "styles") !== FALSE){
        if (($pos = strpos($folder, "/")) !== FALSE) { // caso tenha uma sub-pasta
            $sub_folder = substr($folder, $pos+1);
            $folder = $FOLDER . "css/$sub_folder";
        } else { $folder = $FOLDER . "css/"; } // caso não tenha sub-pasta, apenas 'view/css'
    } else if (strpos($folder, "fontawesome") !== FALSE){
        if (($pos = strpos($folder, "/")) !== FALSE) {
            $sub_folder = substr($folder, $pos+1);
            $folder = $FOLDER . "fonts/fontawesome/$sub_folder";
        } else { $folder = $FOLDER . "fonts/fontawesome/"; }
    } else if (strpos($folder, "scripts") !== FALSE){
        if (($pos = strpos($folder, "/")) !== FALSE) {
            $sub_folder = substr($folder, $pos+1);
            $folder = $FOLDER . "js/$sub_folder";
        } else { $folder = $FOLDER . "js/"; }
    } else {
        header("HTTP/1.1 404 Not Found ");
        exit;
    }

    // se $folder não terminar com / então, adicione uma no final
    if (endsWith($folder, "/") == false) { $folder .= "/"; }
} else { // se PASTA e URL não foram informadas reponde com status HTTP 400
    header("HTTP/1.1 400 Bad Request");
    exit;
}

// Protecao de download de arquivos
if (strpos($folder, "..") !== false || strpos($urls, "..") !== false) {
    header("Location: $_FQDN"); exit;
}



if (endsWith($urls, "js") == true) { // JAVASCRIPT
    header("Content-type: text/javascript");

    $files = explode(",", $urls); // print_r(explode(",", $file)); // Array
    if (count($files) > 1) { // se houver mais de um elemento no Array
        header("Cache-Control: must-revalidate");

        foreach ($files as $js) {

            $js_file = $folder . $js;
            if (file_exists($js_file)) {
                if ((strpos($js_file, ".min.js") !== FALSE)) { // IGNORADO SE JA ESTIVER MINIFICADO > Copyright
                    echo file_get_contents("$js_file");
                } else { echo minify_JS($js_file); }

            } else { // echo "Arquivo Não Encontrado <br><br> &emsp;&emsp;&emsp;" . $folder . $urls;
                header("HTTP/1.1 404 Not Found ");
                exit;
            }
        } // foreach
    } else { // count
        $js = $folder . $files[0];

        if (file_exists($js)) {
            if ((strpos($js, ".min.js") !== FALSE)) { // IGNORADO SE JA ESTIVER MINIFICADO > Copyright
                FILE_MODIFIED($js); // se for um arquivo único, permitir salvar em CACHE = FILE_MODIFIED()
                echo file_get_contents("$js");
                exit;
            } else {

                FILE_MODIFIED($js);
                echo minify_JS($js);
                exit;
            }
        } else {
            header("HTTP/1.1 404 Not Found ");
            exit;
        }
    } // count

} else if (endsWith($urls, "css") == true) { // CSS
    header("Content-type: text/css");

    $files = explode(",", $urls);
    if (count($files) > 1) { // se houver mais de um elemento no Array
        header("Cache-Control: must-revalidate");
        
        foreach ($files as $css) {

            $css_file = $folder . $css;
            if (file_exists($css_file)) {
                if ((strpos($css_file, ".min.css") !== FALSE)) { // IGNORADO SE JA ESTIVER MINIFICADO > Copyright
                    echo file_get_contents("$css_file");
                } else { echo minify_CSS($css_file); }

            } else {
                header("HTTP/1.1 404 Not Found ");
                exit;
            }
        } // foreach
    } else { // count
        $css = $folder . $files[0];
        if (file_exists($css)) {
            if ((strpos($css, ".min.css") !== FALSE)) { // IGNORADO SE JA ESTIVER MINIFICADO > Copyright
                FILE_MODIFIED($css); // se for um arquivo único, permitir salvar em CACHE = FILE_MODIFIED()
                echo file_get_contents("$css");
                exit;
            } else {

                FILE_MODIFIED($css);
                echo minify_CSS($css);
                exit;
            }
        } else {
            header("HTTP/1.1 404 Not Found ");
            exit;
        }
    } // count
} else {
    // echo "O nome dos arquivos deve terminar com <b>.JS</b> ou <b>.CSS</b> <br><br> &emsp;&emsp;&emsp;" . $folder . $urls;
    header("HTTP/1.1 404 Not Found ");
    exit;
}

function endsWith($haystack, $needle) {
    $length = strlen($needle);
    $start  = $length * -1; //negative
    return (substr($haystack, $start) === $needle);
}



/**
 * -----------------------------------------------------------------------------------------
 * Fonte `https://gist.github.com/gopalindians/4e86bc975993046c238ff3074174e00f`
 * Baseado em `https://github.com/mecha-cms/mecha-cms/blob/master/engine/kernel/converter.php`
 * -----------------------------------------------------------------------------------------
**/

// HTML Minifier
function minify_HTML($input) {
    if(trim($input) === "") return $input;
    // Remove extra white-space(s) between HTML attribute(s)
    $input = preg_replace_callback('#<([^\/\s<>!]+)(?:\s+([^<>]*?)\s*|\s*)(\/?)>#s', function($matches) {
        return '<' . $matches[1] . preg_replace('#([^\s=]+)(\=([\'"]?)(.*?)\3)?(\s+|$)#s', ' $1$2', $matches[2]) . $matches[3] . '>';
    }, str_replace("\r", "", $input));
    // Minify inline CSS declaration(s)
    if(strpos($input, ' style=') !== false) {
        $input = preg_replace_callback('#<([^<]+?)\s+style=([\'"])(.*?)\2(?=[\/\s>])#s', function($matches) {
            return '<' . $matches[1] . ' style=' . $matches[2] . minify_css($matches[3]) . $matches[2];
        }, $input);
    }
    return preg_replace(
        array(
            // t = text
            // o = tag open
            // c = tag close
            // Keep important white-space(s) after self-closing HTML tag(s)
            '#<(img|input)(>| .*?>)#s',
            // Remove a line break and two or more white-space(s) between tag(s)
            '#(<!--.*?-->)|(>)(?:\n*|\s{2,})(<)|^\s*|\s*$#s',
            '#(<!--.*?-->)|(?<!\>)\s+(<\/.*?>)|(<[^\/]*?>)\s+(?!\<)#s', // t+c || o+t
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<[^\/]*?>)|(<\/.*?>)\s+(<\/.*?>)#s', // o+o || c+c
            '#(<!--.*?-->)|(<\/.*?>)\s+(\s)(?!\<)|(?<!\>)\s+(\s)(<[^\/]*?\/?>)|(<[^\/]*?\/?>)\s+(\s)(?!\<)#s', // c+t || t+o || o+t -- separated by long white-space(s)
            '#(<!--.*?-->)|(<[^\/]*?>)\s+(<\/.*?>)#s', // empty tag
            '#<(img|input)(>| .*?>)<\/\1\x1A>#s', // reset previous fix
            '#(&nbsp;)&nbsp;(?![<\s])#', // clean up ...
            // Force line-break with `&#10;` or `&#xa;`
            '#&\#(?:10|xa);#',
            // Force white-space with `&#32;` or `&#x20;`
            '#&\#(?:32|x20);#',
            // Remove HTML comment(s) except IE comment(s)
            '#\s*<!--(?!\[if\s).*?-->\s*|(?<!\>)\n+(?=\<[^!])#s'
        ),
        array(
            "<$1$2</$1\x1A>",
            '$1$2$3',
            '$1$2$3',
            '$1$2$3$4$5',
            '$1$2$3$4$5$6$7',
            '$1$2$3',
            '<$1$2',
            '$1 ',
            "\n",
            ' ',
            ""
        ),
    $input);
}

// CSS Minifier => http://ideone.com/Q5USEF + improvement(s)
function minify_CSS($input_File) {
    $input = file_get_contents($input_File); // echo $input;

    if(trim($input) === "") return $input;

    // Force white-space(s) in `calc()`
    if(strpos($input, 'calc(') !== false) {
        $input = preg_replace_callback('#(?<=[\s:])calc\(\s*(.*?)\s*\)#', function($matches) {
            return 'calc(' . preg_replace('#\s+#', "\x1A", $matches[1]) . ')';
        }, $input);
    }
    return preg_replace(
        array(
            // Remove comment(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')|\/\*(?!\!)(?>.*?\*\/)|^\s*|\s*$#s',
            // Remove unused white-space(s)
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/))|\s*+;\s*+(})\s*+|\s*+([*$~^|]?+=|[{};,>~+]|\s*+-(?![0-9\.])|!important\b)\s*+|([[(:])\s++|\s++([])])|\s++(:)\s*+(?!(?>[^{}"\']++|"(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')*+{)|^\s++|\s++\z|(\s)\s+#si',
            // Replace `0(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)` with `0`
            '#(?<=[\s:])(0)(cm|em|ex|in|mm|pc|pt|px|vh|vw|%)#si',
            // Replace `:0 0 0 0` with `:0`
            '#:(0\s+0|0\s+0\s+0\s+0)(?=[;\}]|\!important)#i',
            // Replace `background-position:0` with `background-position:0 0`
            '#(background-position):0(?=[;\}])#si',
            // Replace `0.6` with `.6`, but only when preceded by a white-space or `=`, `:`, `,`, `(`, `-`
            '#(?<=[\s=:,\(\-]|&\#32;)0+\.(\d+)#s',
            // Minify string value
            '#(\/\*(?>.*?\*\/))|(?<!content\:)([\'"])([a-z_][-\w]*?)\2(?=[\s\{\}\];,])#si',
            '#(\/\*(?>.*?\*\/))|(\burl\()([\'"])([^\s]+?)\3(\))#si',
            // Minify HEX color code
            '#(?<=[\s=:,\(]\#)([a-f0-6]+)\1([a-f0-6]+)\2([a-f0-6]+)\3#i',
            // Replace `(border|outline):none` with `(border|outline):0`
            '#(?<=[\{;])(border|outline):none(?=[;\}\!])#',
            // Remove empty selector(s)
            '#(\/\*(?>.*?\*\/))|(^|[\{\}])(?:[^\s\{\}]+)\{\}#s',
            '#\x1A#'
        ),
        array(
            '$1',
            '$1$2$3$4$5$6$7',
            '$1',
            ':0',
            '$1:0 0',
            '.$1',
            '$1$3',
            '$1$2$4$5',
            '$1$2$3',
            '$1:0',
            '$1$2',
            ' '
        ),
    $input);
}

// JavaScript Minifier
function minify_JS($input_File) {
    $input = file_get_contents($input_File); // echo $input;

    if(trim($input) === "") return $input;
    return preg_replace(
        array(
            // Remove comment(s)
            '#\s*("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\')\s*|\s*\/\*(?!\!|@cc_on)(?>[\s\S]*?\*\/)\s*|\s*(?<![\:\=])\/\/.*(?=[\n\r]|$)|^\s*|\s*$#',
            // Remove white-space(s) outside the string and regex
            '#("(?:[^"\\\]++|\\\.)*+"|\'(?:[^\'\\\\]++|\\\.)*+\'|\/\*(?>.*?\*\/)|\/(?!\/)[^\n\r]*?\/(?=[\s.,;]|[gimuy]|$))|\s*([!%&*\(\)\-=+\[\]\{\}|;:,.<>?\/])\s*#s',
            // Remove the last semicolon
            '#;+\}#',
            // Minify object attribute(s) except JSON attribute(s). From `{'foo':'bar'}` to `{foo:'bar'}`
            '#([\{,])([\'])(\d+|[a-z_]\w*)\2(?=\:)#i',
            // --ibid. From `foo['bar']` to `foo.bar`
            '#([\w\)\]])\[([\'"])([a-z_]\w*)\2\]#i',
            // Replace `true` with `!0`
            '#(?<=return |[=:,\(\[])true\b#',
            // Replace `false` with `!1`
            '#(?<=return |[=:,\(\[])false\b#',
            // Clean up ...
            '#\s*(\/\*|\*\/)\s*#'
        ),
        array(
            '$1',
            '$1$2',
            '}',
            '$1$3',
            '$1.$3',
            '!0',
            '!1',
            '$1'
        ),
    $input);
}

function FILE_MODIFIED($file) {
    $flmtime = filemtime($file);
    header('ETag: "' . md5($flmtime . $file) . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $flmtime) . ' GMT'); // header('Last-Modified: ' . $flmtime);
    header('Cache-Control: private,max-age=86400');

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $flmtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($flmtime . $file)) {
            header('HTTP/1.1 304 Not Modified');
            return TRUE;
        }
    }
    return FALSE;
}
