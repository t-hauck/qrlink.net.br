<?php

require_once dirname(dirname(__FILE__)) . "/conectar_SQL.php";


$conexao = Conectar::sql();

foreach (range(1, 10) as $num) {
    $sql = "INSERT INTO url_shorten (original_url, short_code) VALUES ('test_link', 'code: $num')";
    $sqlResult = $conexao->query($sql);
}

if ($sqlResult) {
        echo "  \nLinks Inseridos com SUCESSO \n\n";
} else  echo "  \n- FALHA \n\n";
