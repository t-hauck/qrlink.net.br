<?php
$bd = new SQLite3("links.db");

////////
$sql = "DROP TABLE IF EXISTS url_shorten";
if ($bd->exec($sql)) echo "\ntabela 'url_shorten' apagada\n";

////////
$sql = "CREATE TABLE IF NOT EXISTS url_shorten (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            original_url text NOT NULL,
            short_code varchar(10) NOT NULL,
            short_code_password TEXT NULL,

            access int(11) NOT NULL DEFAULT 0,
            last_access timestamp NULL,

            access_attempts int(11) NULL,
            last_access_attempt timestamp NULL,

            added_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
            )
    ";
if ($bd->exec($sql))
    echo "\ntabela 'url_shorten' criada \n"; 
else 
    echo "\nerro ao criar tabela 'url_shorten' \n"; 

////////
//  $sql = "INSERT INTO url_shorten (original_url, short_code) VALUES (
//            'http://127.0.0.1/link_de_teste', 'teste') ";

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
