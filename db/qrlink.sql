CREATE DATABASE qrlink;

CREATE TABLE IF NOT EXISTS qrlink.url_shorten (
 id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
 url tinytext NOT NULL,
 short_code varchar(50) NOT NULL,
 hits int(11) NOT NULL,
 last_acess timestamp NULL,
 added_date timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=uf8;

INSERT INTO qrlink.url_shorten (url, short_code, hits) VALUES (
        'http://127.0.0.1/link_de_teste', 'teste', '0')


        