CREATE DATABASE IF NOT EXISTS qrlink;

CREATE TABLE IF NOT EXISTS qrlink.url_shorten (
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        original_url text NOT NULL,
        short_code varchar(10) NOT NULL,
        short_code_password TEXT NULL,

        access int(11) NOT NULL DEFAULT 0,
        last_access timestamp NULL,

        access_attempts int(11) NULL,
        last_access_attempt timestamp NULL,

        added_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

        blocked_url BOOLEAN NOT NULL DEFAULT FALSE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- Criação de evento para apagar automaticamente links sem acessos, não compativel com SQLite
-- - para banco de dados SQLite, usar script autoDelete.sqlite.php
-- Evento inicia no dia em que for criado, e será executado sempre a meia-noite
CREATE EVENT IF NOT EXISTS qrlink.delete_links_without_access ON SCHEDULE EVERY 1 DAY STARTS TIMESTAMP(CURRENT_DATE, '00:00:00') DO DELETE FROM qrlink.url_shorten WHERE last_access < (NOW() - INTERVAL 3 MONTH);
