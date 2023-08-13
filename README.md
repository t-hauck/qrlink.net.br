# qrlink.net.br
![Built with PHP](https://img.shields.io/badge/Built%20with-php-red?style=for-the-badge&logo=php)

<br>


### Baseado Em
1. https://nomadphp.com/blog/64/creating-a-url-shortener-application-in-php-mysql
2. https://upload.disroot.org/files


### Notas
- Forçar atualização da tabela em /links antes do tempo definido

```javascript
var link_codes = JSON.parse(localStorage.getItem("links"));
local_RequestData("update", link_codes);
```
