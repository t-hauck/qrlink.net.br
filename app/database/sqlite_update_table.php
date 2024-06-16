<?php
$bd = new SQLite3("links.db");

$sql = "ALTER TABLE url_shorten ADD COLUMN last_acess timestamp NULL";

if ($bd->exec($sql)) 
    echo "\ntabela 'url_shorten' alterada com sucesso\n"; 
else
    echo "\nfalha ao alterar tabela 'url_shorten'\n";
