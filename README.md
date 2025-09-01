# site-contato
Programa de teste - disciplina da faculdade

# criação da instância ec2
Seguir passos para criação da instância ec2 (aws) com ubuntu
Após, conectar a instância e o par de chaves via cmd

# Playbook: Provisionamento e Deploy — Ubuntu Server

Este documento lista, em ordem, os comandos necessários para provisionar do zero uma instância Ubuntu Server e implantar o projeto "site-contato" que está no branch main do repositório.

Observação: o repositório contém os arquivos na raiz: README.md, contato.php, db/, estilo.css, index.html, listar.php, script.js, squema.sql.

1) Atualizar sistema
```bash
sudo apt update && sudo apt upgrade -y
```

2) Instalar Apache, PHP e dependências
```bash
sudo apt install -y git apache2 php php-mysql php-mbstring php-xml mariadb-server
```

4) Habilitar e iniciar Apache e mariadb
```bash
sudo systemctl enable --now apache2
sudo systemctl enable --now mariadb
```

5) Preparar /var/www e clonar repositório
```bash
# Remover arquivos padrões, se necessário
sudo rm -rf /var/www/html/*
# Ajustar permissões
sudo chown -R ubuntu:www-data /var/www/html
sudo find /var/www -type d -exec chmod 2775 {} \;
sudo find /var/www -type f -exec chmod 0664 {} \;
cd /var/www/html
# Remover index padrão
sudo rm -f index.html
# Clonar repositório com usuário ubuntu
sudo -u ubuntu git clone https://github.com/Erin-Silva/site-contato.git .
#(configurar mariadb)
sudo mysql_secure_installation
(escolher password)
```

6) Criar banco e usuário no MariaDB (entrar como root)
```bash
sudo mysql -u root -p
-- então no prompt do MariaDB execute (substitua senha_segura_aqui por uma senha forte):
CREATE DATABASE site_contato CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE site_contato;
CREATE USER 'site_user'@'localhost' IDENTIFIED BY 'senha_segura_aqui';
GRANT INSERT, SELECT ON site_contato.* TO 'site_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

7) Importar schema (arquivo squema.sql que está na raiz do repositório)
```bash
sudo mysql -u root -p site_contato < /var/www/html/squema.sql
# ou, dentro do cliente mysql:
# SOURCE /var/www/html/squema.sql;
```

8) Criar arquivo de variáveis DB (db/.env) e ajustar permissões
```bash
# Conteúdo sugerido para /var/www/html/db/.env (não comitar)
DB_HOST=localhost
DB_NAME=site_contato
DB_USER=site_user
DB_PASS=senha_segura_aqui
DB_PORT=3306
```

# Permissões
```bash
sudo chown www-data:www-data /var/www/html/db/.env
sudo chmod 640 /var/www/html/db/.env
```

9) Testar o site
```bash
Abra no navegador: http://IP_DA_INSTANCIA/  
Teste enviar mensagem (index.html -> contato.php) e verifique em listar.php.
```

10) Diferenças importantes entre Amazon Linux 2023 e Ubuntu (resumo)
```bash
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
```

11) Boas práticas e observações finais
```bash
- Verifique o nome exato do arquivo squema.sql no repositório (atenção à ortografia).
- Não comitar /db/.env; inclua no .gitignore.
- Use senhas fortes e não compartilhe arquivos .pem ou credenciais.
- Se o git clone por SSH falhar, você pode clonar via HTTPS:
  sudo git clone https://github.com/Erin-Silva/site-contato.git /var/www/html
```

