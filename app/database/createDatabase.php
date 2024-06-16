<?php

require_once dirname(__FILE__) . "/Conectar.php";


$conexao = Conectar::sql();
$sqlPath = dirname(__FILE__) . "/qrlink.sql";


if (file_exists($sqlPath)){
    $sqlCommands = file_get_contents($sqlPath);
    $sqlCommands = trim($sqlCommands);

    // Separa os comandos usando o ponto e vírgula como delimitador
    $commands = explode(";", $sqlCommands);

    foreach ($commands as $command) {
        $command = trim($command);
        if (!empty($command)) {
            $conexao->exec($command);
        }
    }
    exit;
}else {
    echo "Arquivo Não Encontrado: " . $sqlPath . "\n";
    exit;
}
