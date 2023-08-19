ALTER TABLE `url_shorten` MODIFY `url` TEXT;
ALTER TABLE url_shorten ADD short_code_password TEXT NULL;
ALTER TABLE qrlink.url_shorten ADD blocked_url BOOLEAN NOT NULL DEFAULT FALSE;
UPDATE qrlink.url_shorten SET blocked_url = true WHERE short_code = '4387ea';


-- ### ---
-- Link de teste inserido na criação do banco
-- INSERT INTO qrlink.url_shorten (original_url, short_code) VALUES (
--        'http://127.0.0.1/link_de_teste', 'teste')



-- ### ---
-- Estatísticas sobre um link curto específico exibidas ao usuário 
-- SELECT * FROM UserStats_view;
-- DROP VIEW IF EXISTS UserStats_view;
-- CREATE VIEW UserStats_view AS SELECT url, short_code, hits, last_acess FROM url_shorten;
