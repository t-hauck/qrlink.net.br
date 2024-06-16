# qrlink.net.br
![Built with PHP](https://img.shields.io/badge/Built%20with-php-red?style=for-the-badge&logo=php)

<br>

Encurtador de URL desenvolvido com PHP e banco de dados MySQL


### Baseado Em
1. https://nomadphp.com/blog/64/creating-a-url-shortener-application-in-php-mysql
2. https://upload.disroot.org/files


### Notas

- Atualizar as estatísticas do servidor antes do tempo definido

```javascript
serverstats_getData("update");
```

- Atualizar tabela da página /links antes do tempo definido

```javascript
local_RequestData("update", JSON.parse(localStorage.getItem("links")));
```


### Roadmap
- Criação de página de contato para reportar link malicioso

- Permitir criação de links curtos via API
O usuário deverá usar um formulário de contato para solicitar permissão para criar links por uma rota do sistema (API).
Será necessário o uso de uma chave para autenticação, apenas quem usar uma chave autorizada poderá encurtar links pela API.
As chaves que não salvarem nenhum link em 1 ano serão apagadas automaticamente do banco de dados.
O sistema permitirá cadastro e visualização de estatísticas de links apenas se informado o token gerado anteriormente.

### Requisitos
- PHP >= 8.2
- MySQL >= 8.0
- MariaDB >= 10.6.16
- Apache >= 2.4
- Docker >= 24.0.5
- docker-compose >= 1.29.2


### BUGS

#### Links
--
Redirecionamento de links com senha não funciona.

#### Página Administrativa
--
Páginação da tabela não funciona, necessário adicionar e remover evento de click no javascript.

--
Notificação de estatísticas atualizadas aparece sempre, mesmo sem alterações.
