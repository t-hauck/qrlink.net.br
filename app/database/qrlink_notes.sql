ALTER TABLE `url_shorten` MODIFY `url` TEXT;
ALTER TABLE url_shorten ADD short_code_password TEXT NULL;
ALTER TABLE qrlink.url_shorten ADD blocked_url BOOLEAN NOT NULL DEFAULT FALSE;
ALTER TABLE qrlink.url_shorten ADD blocking_date DATETIME;
ALTER TABLE qrlink.url_shorten ADD last_block TIMESTAMP AFTER blocked;
UPDATE qrlink.url_shorten SET blocked_url = true WHERE short_code = '4387ea';


SELECT * FROM qrlink.url_shorten WHERE (blocked = FALSE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE()) OR (blocked = TRUE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE());

ALTER EVENT qrlink.block_unblock_links ON SCHEDULE EVERY 1 DAY STARTS TIMESTAMP(CURRENT_DATE, '00:00:00') DO UPDATE qrlink.url_shorten SET blocked = CASE WHEN blocked = FALSE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE THEN TRUE WHEN blocked = TRUE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE THEN FALSE ELSE blocked END, blocking_date = NULL, last_block = CURRENT_TIMESTAMP WHERE (blocked = FALSE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE) OR (blocked = TRUE AND blocking_date IS NOT NULL AND blocking_date = CURRENT_DATE);
