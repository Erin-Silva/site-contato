# site-contato
Programa de teste - disciplina da faculdade


# Playbook: Provisionamento e Deploy — Ubuntu Server

Este documento lista, em ordem, os comandos necessários para provisionar do zero uma instância Ubuntu Server e implantar o projeto "site-contato" que está no branch main do repositório.

Observação: o repositório contém os arquivos na raiz: README.md, contato.php, db/, estilo.css, index.html, listar.php, script.js, squema.sql.

> Substitua Erin-Silva pelo seu usuário do GitHub antes de executar os comandos.

1) Atualizar sistema
sudo apt update && sudo apt upgrade -y

2) Instalar Apache, PHP e dependências
sudo apt install -y apache2 php libapache2-mod-php php-mysql php-mbstring git

3) Habilitar e iniciar Apache
sudo systemctl enable --now apache2

4) (Opcional) Instalar MariaDB server
sudo apt install -y mariadb-server
sudo systemctl enable --now mariadb
sudo mysql_secure_installation

5) Preparar /var/www/html e clonar repositório
sudo rm -rf /var/www/html/*
sudo git clone git@github.com:Erin-Silva/site-contato.git /var/www/html
sudo chown -R www-data:www-data /var/www/html
sudo find /var/www/html -type d -exec chmod 755 {} \;
sudo find /var/www/html -type f -exec chmod 644 {} \;

6) Criar banco e usuário no MariaDB (entrar como root)
sudo mysql
-- então no prompt do MariaDB execute (substitua senha_segura_aqui por uma senha forte):
CREATE DATABASE site_contato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'site_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
GRANT INSERT, SELECT ON site_contato.* TO 'site_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;

7) Importar schema (arquivo squema.sql que está na raiz do repositório)
sudo mysql -u root < /var/www/html/squema.sql
# ou, dentro do cliente mysql:
# SOURCE /var/www/html/squema.sql;

8) Criar arquivo de variáveis DB (db/.env) e ajustar permissões
# Conteúdo sugerido para /var/www/html/db/.env (não comitar)
DB_HOST=localhost
DB_NAME=site_contato
DB_USER=site_user
DB_PASS=senha_segura_aqui
DB_PORT=3306

# Permissões
sudo chown www-data:www-data /var/www/html/db/.env
sudo chmod 640 /var/www/html/db/.env

9) Testar o site
Abra no navegador: http://IP_DA_INSTANCIA/  
Teste enviar mensagem (index.html -> contato.php) e verifique em listar.php.

10) Habilitar HTTPS com Let's Encrypt (opcional)
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache

11) Diferenças importantes entre Amazon Linux 2023 e Ubuntu (resumo)
- Gerenciador de pacotes:
  - Amazon Linux: sudo dnf install pacote
  - Ubuntu: sudo apt install pacote
- Serviço Apache:
  - Amazon Linux: serviço nomeado httpd (sudo systemctl start httpd)
  - Ubuntu: serviço apache2 (sudo systemctl start apache2)
- Usuário do Apache:
  - Amazon Linux: apache
  - Ubuntu: www-data
  Ajuste chown/chmod conforme o sistema.
- Paths de configuração:
  - Amazon Linux: /etc/httpd/
  - Ubuntu: /etc/apache2/

12) Boas práticas e observações finais
- Verifique o nome exato do arquivo squema.sql no repositório (atenção à ortografia).
- Não comitar /db/.env; inclua no .gitignore.
- Use senhas fortes e não compartilhe arquivos .pem ou credenciais.
- Se o git clone por SSH falhar, você pode clonar via HTTPS:
  sudo git clone https://github.com/Erin-Silva/site-contato.git /var/www/html


