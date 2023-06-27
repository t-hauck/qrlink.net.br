<?php
$bd = new SQLite3("links.db");

////////
$sql = "DROP TABLE IF EXISTS url_shorten";
if ($bd->exec($sql)) echo "\ntabela 'url_shorten' apagada\n";

////////
$sql = "CREATE TABLE IF NOT EXISTS url_shorten (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            url text NOT NULL,
            short_code varchar(50) NOT NULL,
            short_code_password TEXT NULL;
            hits int(11) NOT NULL,
            last_acess timestamp NULL,
            added_date timestamp NULL DEFAULT CURRENT_TIMESTAMP
            )
    ";
if ($bd->exec($sql))
    echo "\ntabela 'url_shorten' criada \n"; 
else 
    echo "\nerro ao criar tabela 'url_shorten' \n"; 

////////
$sql = "
        INSERT INTO url_shorten (url, short_code, hits) VALUES (
        'http://127.0.0.1/link_de_teste', 'teste', '0')
    ";
if ($bd->exec($sql)) 
    echo "\nLink inserido com sucesso, short_code: teste \n"; 
else 
    echo "\nerro ao inserir Link de teste \n";




////////
// $sql = "DROP VIEW IF EXISTS UserStats_view;";
// if ($bd->exec($sql)) echo "\nview 'UserStats_view' apagada\ n"; 

// ////////
// $sql = "CREATE VIEW UserStats_view AS SELECT url, short_code, hits, last_acess FROM url_shorten;";
// if ($bd->exec($sql)) 
//     echo "\nView 'UserStats_view' criada com sucesso \n"; 
// else 
//     echo "\nerro ao criar View 'UserStats_view' \n";
