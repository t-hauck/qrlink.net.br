#!/usr/bin/env bash
##


show_help() {
  cat << EOF
Uso: $0 [opção] [nome_do_banco]
Opções:
  -h, --help      Exibe este texto de ajuda
  -all_db         Faz o backup de todas as bases de dados
  -db             Faz o backup de um único banco de dados
  -restore <file> Restaura um banco de dados a partir de um arquivo .SQL
  -config         Exibe o conteúdo do arquivo .env

EOF
}

if [[ "$1" == "--help" || "$1" == "-h" ]]; then
  show_help
  exit 0
fi


SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
PARENT_DIR="$(dirname "$SCRIPT_DIR")"
ENV_FILE="$PARENT_DIR/.env"

if [ -f "$ENV_FILE" ]; then
  source "$ENV_FILE"
else
  echo -e "- Arquivo .env Não Encontrado \n"
  exit 1
fi

# Variaveis
DB_HOST="$server"        # Host do banco de dados
DB_USER="$user"          # Nome de usuário do banco de dados
DB_PASS="$pass"          # Senha do banco de dados
DB_NAME="$database"      # Nome do banco de dados
BACKUP_DIR="$PWD/backup" # Diretório de destino para os backups

# Verifica se o diretório de backup existe, caso contrário, cria-o
if [ ! -d "$BACKUP_DIR" ]; then
  mkdir -p "$BACKUP_DIR"
fi


# --------------------------------------- #

# Fazer o backup de uma única base de dados
backup_single_database() {
  local DATABASE="$1"
  local FILENAME="$BACKUP_DIR/$DATABASE-$(date +%Y%m%d%H%M%S).sql"
  mysqldump -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" "$DATABASE" > "$FILENAME"
  echo "Backup do banco de dados $DATABASE realizado com sucesso em $FILENAME"
}

# Função para restaurar um banco de dados completo a partir de um arquivo .SQL
restore_database() {
  if [ -f "$1" ]; then
    echo "Restaurando banco de dados a partir do arquivo: $1"
    
    # Extrai o nome do banco de dados do arquivo de mysqldump
    DB_NAME_dump=$(grep -oP --color=never "(?<=Database: )\w+" "$1")
    
    # Verifica se o banco de dados existe
    db_exists=$(mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "SHOW DATABASES LIKE '$DB_NAME_dump'" | grep "$DB_NAME_dump")
    
    if [ -z "$db_exists" ]; then
      echo -e "\n- O Banco de Dados '$DB_NAME_dump' Não Existe"
      read -p "Deseja criar o banco de dados '$DB_NAME_dump'? (s/n): " choice
      
      if [ "$choice" == "s" ] || [ "$choice" == "S" ]; then
        # Cria o banco de dados
        mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" -e "CREATE DATABASE $DB_NAME_dump"
        echo -e "\nBanco de Dados '$DB_NAME_dump' Criado com Sucesso"
      else
        echo "Operação cancelada. O banco de dados $DB_NAME_dump não foi restaurado."
        return
      fi
    fi
    
    # Restaura o banco de dados
    output=$(mysql -u "$DB_USER" -p"$DB_PASS" -h "$DB_HOST" "$DB_NAME_dump" < "$1" 2>&1)
    if [[ $output == *"ERROR"* ]]; then
      echo "Ocorreu um erro durante a restauração do banco de dados:"
      echo -e "\n$output \n\n"
      exit 1
    else
      echo -e "Restauração Concluída com Sucesso \n\n"
      exit 0
    fi
  else
    echo "- Arquivo Não Encontrado: $1"
    exit 1
  fi
}

# --------------------------------------- #


if [ "$1" == "-all_db" ]; then # Backup de todas as bases de dados
  DB_LIST=$(mysql -h "$DB_HOST" -u "$DB_USER" -p"$DB_PASS" -e "SHOW DATABASES;" | grep -Ev "(Database|information_schema|performance_schema)")

  for DATABASE in $DB_LIST; do
    backup_single_database "$DATABASE"
  done
  
elif [ "$1" == "-db" ]; then # Backup de um único banco de dados
  if [ "$2" ]; then
    backup_single_database "$2"
  else
    echo "- Nome do Banco de Dados não Fornecido"
    read -p "Fazer backup do banco de dados definido no arquivo .env (s/n): " choice
    if [ "$choice" == "s" ]; then
        echo ""
        backup_single_database "$DB_NAME"
        echo ""
    else
        echo ""
        show_help
        exit 1
    fi

  fi

elif [ "$1" == "-restore" ]; then
  if [ -n "$2" ]; then # Verifica se o arquivo .SQL foi fornecido como argumento
    restore_database "$2"
  else
    echo "- Arquivo .SQL Não Fornecido"
    exit 1
  fi
  exit 0

elif [ "$1" == "-config" ]; then # Exibe o conteúdo do arquivo .env
    echo -e "- Conteúdo do Arquivo [.env] $ENV_FILE \n\n"
    awk -F '=' '{gsub(/^[[:space:]]+|[[:space:]]+$/, ""); printf "%-20s %s\n", $1, substr($0, index($0, "=")+1)}' $ENV_FILE
    echo ""
else
  echo -e "- Argumento Inválido \n\n"
  show_help
  exit 1
fi
