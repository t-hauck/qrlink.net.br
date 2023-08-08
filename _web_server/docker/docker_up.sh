#!/usr/bin/env bash
# script usado SEM docke-compose
##

HostBasePath="/var/www/hauck.net.br/qrlink"
HostDockerPath="$HostBasePath/_web_server/docker"

HostDockerPath_SSL="$HostDockerPath/ssl_certs"
HostWEB_Logs="$HostDockerPath/apache_logs"




# Self-signed SSL certificate valid for 100 years
openssl req -new -newkey rsa:4096 -days 36500 -nodes -x509 -subj "/C=US/ST=State/L=City/O=Organization/CN=localhost" -keyout $HostDockerPath_SSL/apache-selfsigned.key -out $HostDockerPath_SSL/apache-selfsigned.crt

# Create a strong Diffie-Hellman group for increased security
## openssl dhparam -out /etc/ssl/certs/dhparam.pem 2048

chmod 600 $HostDockerPath_SSL/apache-selfsigned.crt $HostDockerPath_SSL/apache-selfsigned.key






docker build --tag qrlink . || exit


docker run -d --name qrlink \
   -p 8101:80  \
   -p 8102:443 \
   -v $HostBasePath:/var/www/html \
   -v $HostWEB_Logs:/var/log/apache2 \
   qrlink || exit


# systemctl stop apache2
# docker run -d --name qrlink \
#    --network host \
#    -v $HostBasePath:/var/www/html \
#    -v $HostWEB_Logs:/var/log/apache2 \
#    qrlink || exit
