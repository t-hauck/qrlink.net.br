-- ALTER TABLE `url_shorten` MODIFY `url` TEXT;
-- ALTER TABLE url_shorten ADD short_code_password TEXT NULL;


CREATE DATABASE qrlink;

---- Tabela principal do banco de dados
CREATE TABLE IF NOT EXISTS qrlink.url_shorten (
        id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        original_url text NOT NULL,
        short_code varchar(10) NOT NULL,
        short_code_password TEXT NULL,

        access int(11) NOT NULL DEFAULT 0,
        last_access timestamp NULL,

        access_attempts int(11) NULL,
        last_access_attempt timestamp NULL,

        added_date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

---- Link de teste inserido na criação do banco
INSERT INTO qrlink.url_shorten (original_url, short_code) VALUES (
        'http://127.0.0.1/link_de_teste', 'teste')












---- Estatísticas sobre um link curto específico exibidas ao usuário 
-- SELECT * FROM UserStats_view;
-- DROP VIEW IF EXISTS UserStats_view;
-- CREATE VIEW UserStats_view AS SELECT url, short_code, hits, last_acess FROM url_shorten;
