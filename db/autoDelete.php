<?php
/*
    - Script para apagar automaticamente links do banco que não tenham nenhum acesso a algum tempo
    - Execute este script UMA VEZ para adicionar uma linha na CRONTAB
    UPDATE qrlink.url_shorten SET last_acess = '2010-10-01 00:00:00' WHERE short_code = '5556af';

    - Remove a chamada deste script da crontab
        cat /etc/crontab |grep -v '$SCRIPT_DIR' > /etc/crontab.tmp
        mv /etc/crontab.tmp /etc/crontab
        chmod 644 /etc/crontab
        chown root:root /etc/crontab
*/

require_once dirname(dirname(__FILE__)) . "/conectar_SQL.php";
$SCRIPT = dirname(__FILE__) . "/" . "autoDelete.php";


$date = date("d-m-Y");

// Execução todo primeiro dia do mês a meia-noite em todos os meses
$CRON_linha = "00 00 1 * * root php $SCRIPT >> $SCRIPT.log";

// Verifica se este arquivo está na crontab
$CRON = shell_exec("grep '$SCRIPT' /etc/crontab");
if ($CRON == FALSE) {
    shell_exec("cat /etc/crontab > /tmp/crontab.tmp"); // Backup temporario do arquivo

    // Adicionadas linhas no final do arquivo
    shell_exec("echo >> /etc/crontab");
    shell_exec("echo '# Apagar links do sistema QR-Link automaticamente' >> /etc/crontab");
    shell_exec("echo '$CRON_linha' >> /etc/crontab");
    echo "> $date \n";
    echo "- Adicionada linha em /etc/crontab para executar este script: \n  $CRON_linha \n";
} else {
    echo "\n\n> $date \n";
    echo "- Já existe uma linha para este script em /etc/crontab \n";
}



$conexao = Conectar::sql();
$AutoDelete_tSql = "3 MONTH"; // 1 YEAR

$sql = "SELECT * FROM url_shorten WHERE last_acess < CURRENT_TIMESTAMP() - INTERVAL $AutoDelete_tSql";
$result_select = $conexao->query($sql);
$list = $result_select->fetchAll(PDO::FETCH_ASSOC);


if ( count($list) == 0 ){ //  return false;
    echo "- Não foi encontrado nenhum registro no banco de dados para ser apagado no período escolhido";
    exit;
} else {
        
    $sql = "DELETE FROM url_shorten WHERE last_acess < CURRENT_TIMESTAMP() - INTERVAL $AutoDelete_tSql";
    $result_delete = $conexao->query($sql);
    if (!$result_delete){
        echo "  \nFalha ao executar DELETE no Banco de Dados";
        echo "Registros Encontrados: " . count($list) . "\n\n\n";
        var_dump($list);
        exit;
    } else {
        
        echo "  \nRegistros Apagados: " . count($list) . "\n\n\n";
        var_dump($list);
        exit;
    }
}
