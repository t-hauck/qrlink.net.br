<?php require_once 'css_Js.php';

$meta_title = "QR-Link &mdash; Encurtador de URL, Criador e Leitor de QRCode";
$meta_description = "Encurtador de URL, Leitor e Gerador de QRCode simples que respeita sua privacidade. Diminua links grandes, acompanhe seus acessos e gerencie QRCodes.";


?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
      <meta charset="utf-8">
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">

      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="mobileoptimized" content="300">
      <meta name="HandheldFriendly" content="true">
      <meta name="keywords" content="encurtador, encurtador de URL, encurtador de link, qrcode, qr code, gerar qrcode, escanear qrcode, ler qrcode, encurtar link whatsapp, encurtar link telegram, qrlink, qr link">

      <meta name="google" content="notranslate">

      <link rel="canonical" href="https://<?= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>">

      <!-- Facebook Meta Tags -->
      <meta property="og:url" content="https://qrlink.net.br/">
      <meta property="og:title" content="<?= $meta_title ?>">
      <meta property="og:description" content="<?= $meta_description ?>">
      <meta property="og:image" content="">

      <!-- Twitter Meta Tags -->
      <meta name="twitter:card" content="summary_large_image">
      <meta property="twitter:domain" content="qrlink.net.br">
      <meta property="twitter:url" content="https://qrlink.net.br/">
      <meta name="twitter:title" content="<?= $meta_title ?>">
      <meta name="twitter:description" content="<?= $meta_description ?>">
      <meta name="twitter:image" content="">

      <link href="/view/img/icons8-3-search-32.png" rel="icon">
      <link href="/view/img/icons8-3-search-32.png" rel="apple-touch-icon">


      <?= returnCSS(); ?>