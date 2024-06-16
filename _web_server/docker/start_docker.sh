#!/usr/bin/env bash
##

HostBasePath="/var/www/websites/qrlink.net.br"
HostDockerPath="$HostBasePath/_web_server/docker"

HostDockerPath_SSL="$HostDockerPath/ssl_certs"
HostWEB_Logs="$HostDockerPath/apache_logs"


if [[ ! -e "$HostDockerPath_SSL/apache-selfsigned.key" && ! -e "$HostDockerPath_SSL/apache-selfsigned.crt" ]]; then
   # Self-signed SSL certificate valid for 100 years
   openssl req -new -newkey rsa:4096 -days 36500 -nodes -x509 -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost" -keyout $HostDockerPath_SSL/apache-selfsigned.key -out $HostDockerPath_SSL/apache-selfsigned.crt
   # Create a strong Diffie-Hellman group for increased security
   ## openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048

   chmod 600 $HostDockerPath_SSL/apache-selfsigned.crt $HostDockerPath_SSL/apache-selfsigned.key
   ls -lsh $HostDockerPath_SSL/apache-selfsigned.crt $HostDockerPath_SSL/apache-selfsigned.key
fi


docker-compose up -d


if [ -f ../../.env ]; then
    source ../../.env
   if [ -z "$user" ] || [ -z "$pass" ]; then
      echo "Arquivo .env não contém todas as variáveis necessárias."
      exit 1
   fi
else
    echo "Arquivo .env não encontrado."
    exit 1
fi

MAX_TRIES=30
while [ "$MAX_TRIES" -gt 0 ]; do
    if mysql -u "$user" -p"$pass" --port 33306 -e "SHOW DATABASES;" > /dev/null 2>&1; then
      docker-compose exec qrlink php /var/www/html/app/database/createDatabase.php
      [[ $? -eq 0 ]] && echo -e "PHP Executado: createDatabase.php \n"
      break
    else
      echo "Tentando conexão com servidor MySQL. Restam $MAX_TRIES tentativas."
      sleep 1
      MAX_TRIES=$((MAX_TRIES - 1))  # Reduz o número de tentativas restantes
    fi
done

if [ "$MAX_TRIES" -eq 0 ]; then
    echo "- Falha ao conectar ao servidor MySQL"
    exit 1
fi
