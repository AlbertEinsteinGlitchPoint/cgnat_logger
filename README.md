# cgnat_logger
CGNAT-LOGGER Mysql para Syslog-NG + Mikrotik



Tutorial instalacão sistema CGNAT LOGGER  

O sistema utiliza o Mikrotik para efetuar os logs e enviar ao Syslog-NG remoto em um servidor de armazenamento de logs.



instalar syslog-ng + apache2 + mysql

1- sudo apt update

2- sudo apt upgrade -y

3- sudo apt get install apache2

4- sudo mysql_secure_installation

5- sudo apt install php php-mysql -y


6- Após instalar o Syslog-NG  vá até a pasta do syslog-ng e edite o arquivo 
   normalmente o arquivo fica no caminho /etc/syslog-ng/syslog-ng.conf

   então via terminal podemos usar 

   nano /etc/syslog-ng/syslog-ng.conf

   E no final do arquivo de configuracão vamos adicionar a config do mikrotik

# MIKROTIK ###########
# Adiciona filtro para mikrotik logs

filter f_mikrotik { host( "IP.DA.SUA.MIKROTIK" ); };
log { source ( s_net ); filter( f_mikrotik ); destination ( df_mikrotik ); };
destination df_mikrotik {
    file("/var/log/mikrotik/${HOST}.${YEAR}.${MONTH}.${DAY}.${HOUR}.log"
    template-escape(no));
};


O Formato do log ficara IP-mikrotik + Ano + Mês + Dia + Hora.log


7- Para verificar a versão do MySQL e confirmar que o serviço está rodando, execute:

mysql --version

sudo systemctl status mysql


8- Para verificar a versão do PHP, execute:
php -v

9- vá para o diretório /var/log/   e crie a pasta mikrotik usando o comando: 
   mkdir mikrotik

10- Vá para o diretório /var/www/html/

11- digite via terminal git clone  para baixar do github o git completo com os arquivos
    
    git clone https://github.com/AlbertEinsteinGlitchPoint/cgnat_logger.git


12 -Para o MySQL, você pode precisar criar um banco de dados e um usuário para o seu projeto. Para fazer isso, faça login no console do MySQL com:
 
mysql -u root -p -e "CREATE DATABASE cgnat_logger;"

mysql -u root -p /var/www/html/cgnat_logger/cgnat_logger < cgnat_logger.sql




em seguida irá solicitar para digitar a senha usada durante a instalacão do mysql-server ou normalmente a senha do usuário ubuntu. 
Após digitar a senha ele irá carregar o banco cgnat_logger.sql

por ultimo após carregar o banco de dados será necessário editar o arquivo db.php para inserir os dados de login usuário e senha para conectar ao banco de dados mysql

nano db.php

$username = "seu-usuario-aqui";  // normalmente dependendo do sistema operativo é root ou qualquer outro usuário que criar durante instalacão do banco de dados mysql
$password = "sua-senha-aqui";
$dbname = "cgnat_logger";





13- aponte o seu apache  em /etc/apache2/sites-available/000-default.conf  

    DocumentRoot /var/www/html  
    altere para 
    DocumentRoot /var/www/html/cgnat_logger


14- acesse o seu mikrotik CGNAT logger dispositivo e crie a seguinte regra em ip/firewall/filter

   IP-do-seu-bloco-privado-cgnat-aqui = 100.64.0.1/24 
 
    abrir o terminal e digitar

    ip firewall filter

    add action=log chain=forward comment="Logs CGNAT" connection-state=new log-prefix=CGNAT src-address=100.64.0.1/24



15- Em seguida criar a regra para enviar os logs do mikrotik para o syslog-ng usando  o seguinte comando via terminal do mikrotik

/system logging action

set 3 remote=ip.servidor.remoto.aqui src-address=ip.do.mikrotik.aqui

/system logging

set 0 disabled=yes

add action=remote prefix=CGNAT topics=firewall

add topics=account

add topics=interface





Se conseguiu efetuar os passos com sucesso aguarde uns minutos e acesse o painel cgnat logger, onde deverá apareer a lista com os arquivos log do lado direito.

Arquivos acima de 200Mb demora quase 2minutos para carregar no banco de dados para filtrar os dados necessários.

para acessar o painel 

http://ip.do.seu.servidor

usuário padrão: admin
senha padrão: admin



após efetuar login por segurança sugiro que crie um novo usuário de acesso e senha, que faça login de novo no painel com o novo usuário e delete o usuário padrão.
