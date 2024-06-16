CREATE DATABASE IF NOT EXISTS qrlink;

CREATE TABLE IF NOT EXISTS qrlink.url_shorten (
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        original_url text NOT NULL,
        short_code varchar(10) NOT NULL,
        short_code_password TEXT,

        access int(11) NOT NULL DEFAULT 0,
        last_access TIMESTAMP,

        access_attempts int(11),
        last_access_attempt TIMESTAMP,

        added_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        blocked BOOLEAN NOT NULL DEFAULT FALSE,
        last_block TIMESTAMP,
        blocking_date TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


-- Criação de evento para apagar automaticamente links sem acessos, não compativel com SQLite
-- Evento inicia no dia em que for criado, e será executado sempre a meia-noite
-- -> para banco de dados SQLite, usar script autoDelete.sqlite.php
CREATE EVENT IF NOT EXISTS qrlink.delete_links_without_access ON SCHEDULE EVERY 1 DAY STARTS TIMESTAMP(CURRENT_DATE, '00:00:00') DO DELETE FROM qrlink.url_shorten WHERE last_access < (NOW() - INTERVAL 3 MONTH) AND blocked = FALSE;

-- Criação de evento para bloquear ou desbloquear links automaticamente na data agendada
-- Evento inicia no dia em que for criado, e será executado sempre a meia-noite
-- -> UPDATE blocked = true|false, blocking_date = NULL, last_block = CURRENT_TIMESTAMP
CREATE EVENT IF NOT EXISTS qrlink.block_unblock_links ON SCHEDULE EVERY 1 DAY STARTS TIMESTAMP(CURRENT_DATE, '00:00:00') DO UPDATE qrlink.url_shorten SET blocked = CASE WHEN blocked = FALSE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE THEN TRUE WHEN blocked = TRUE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE THEN FALSE ELSE blocked END, blocking_date = NULL, last_block = CURRENT_TIMESTAMP WHERE (blocked = FALSE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE) OR (blocked = TRUE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE);
