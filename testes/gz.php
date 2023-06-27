<?php
// https://172.30.7.250/gz.php?file=js/tooltip.js

$file = escapeshellcmd($_GET['file']);
$path_parts = pathinfo($file);
$ext = strtolower($path_parts["extension"]);

// Protecao download arquivos
if (strpos($file, "..") !== false) {
    header("Location: {$_SERVER['HTTP_HOST']}");
    exit();
}
if (strpos($file, "estilos/") === false && strpos($file, "app/") === false && strpos($file, "js/") === false && $ext != "js" && $ext != "css") {
    header("Location: {$_SERVER['HTTP_HOST']}");
    exit();
}

if (file_exists($file)) {
    switch ($ext) {
        case 'html': $type = 'text/html'; break;
        case 'css': $type = 'text/css'; break;
        case 'txt': $type = 'text/plain'; break;
        case 'js': $type = 'text/javascript'; break;
    }

    $flmtime = filemtime($file);
    header('ETag: "' . md5($flmtime . $file) . '"');
    header('Last-Modified: ' . $flmtime);
    header('Cache-Control: max-age=86400');
    header('Cache-Control: private');

    if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
        if ($_SERVER['HTTP_IF_MODIFIED_SINCE'] == $flmtime || str_replace('"', '', stripslashes($_SERVER['HTTP_IF_NONE_MATCH'])) == md5($flmtime . $file)) {
            header('HTTP/1.1 304 Not Modified');
            exit();
        }
    }

    if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
        ob_start("ob_gzhandler");
    } else {
        ob_start();
    }
    header("Content-type: " . $type . "; charset: UTF-8");
    header("Cache-Control: must-revalidate");
    require_once $file;
} else {
    header("Location: {$_SERVER['HTTP_HOST']}");
    exit();
}
?>
