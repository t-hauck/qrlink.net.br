<?php

require_once dirname(dirname(__FILE__)) . "/conectar_SQL.php";


$conexao = Conectar::sql();

$TOTAL=100;
foreach (range(1, $TOTAL) as $num) {
    $sql = "INSERT INTO url_shorten (original_url, short_code) VALUES ('test_link', 'code$num')";
    $sqlResult = $conexao->query($sql);
}


$sql_lastAccess = "UPDATE qrlink.url_shorten SET last_access = '2010-10-01 00:00:00' WHERE original_url = 'test_link'";
$sqlResult = $conexao->query($sql_lastAccess);

if ($sqlResult) {
        echo "  \n=> $TOTAL Links Inseridos com SUCESSO \n\n";
} else  echo "  \n- FALHA \n\n";
