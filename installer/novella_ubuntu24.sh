#!/bin/bash -e

APP_DB='novella'
APP_DB_USER='novello'
APP_DB_PASS=$(< /dev/urandom tr -dc _A-Za-z0-9 | head -c32);

APP_DIR='/var/www/novella'
DATA_DIR='/var/www/data'
CACHE_DIR='/var/www/cache'

HNAME=$(hostname -f)

WITH_DEMO=true
WITH_QGIS=true

function install_qgis_server(){

	RELEASE=$(lsb_release -cs)
	wget --no-check-certificate --quiet -O /etc/apt/keyrings/qgis-archive-keyring.gpg https://download.qgis.org/downloads/qgis-archive-keyring.gpg

	# 3.28.x Firenze 				​-> URIs: https://qgis.org/ubuntu
	# 3.22.x Białowieża LTR	-> URIs: https://qgis.org/ubuntu-ltr
	cat >>/etc/apt/sources.list.d/qgis.sources <<CAT_EOF
Types: deb deb-src
URIs: https://qgis.org/ubuntu
Suites: ${RELEASE}
Architectures: amd64
Components: main
Signed-By: /etc/apt/keyrings/qgis-archive-keyring.gpg
CAT_EOF

	apt-get update -y || true
  apt-get install -y qgis-server
	
	if [ -d /etc/logrotate.d ]; then
		cat >/etc/logrotate.d/qgisserver <<CAT_EOF
/var/log/qgisserver.log {
	su www-data www-data
	size 100M
	notifempty
	missingok
	rotate 3
	daily
	compress
	create 660 www-data www-data
}
CAT_EOF
	fi
	
	mkdir -p ${DATA_DIR}/qgis/
	chown www-data:www-data ${DATA_DIR}/qgis
	
	touch /var/log/qgisserver.log
	chown www-data:www-data /var/log/qgisserver.log

	# Temp fix for https://github.com/qgis/QGIS/issues/59613
	mkdir -p /.cache/QGIS/
	chown -R www-data:www-data /.cache/QGIS/
	ln -s ${CACHE_DIR}/qgis /.cache/QGIS/QGIS3
}

function install_qgis_server_plugins(){
	
	apt-get -y install python3-virtualenv
	
	mkdir -p ${DATA_DIR}/qgis/plugins

	# install plugins manager
	pushd ${DATA_DIR}/qgis/plugins
		virtualenv --python=/usr/bin/python3 --system-site-packages .venv
		source .venv/bin/activate
		
		pip3 install qgis-plugin-manager
		
		export QGIS_PLUGINPATH=${DATA_DIR}/qgis/plugins
		qgis-plugin-manager init
		qgis-plugin-manager update
		qgis-plugin-manager install wfsOutputExtension
	popd

	chown -R www-data:www-data ${DATA_DIR}/qgis/plugins
}

touch /root/auth.txt
export DEBIAN_FRONTEND=noninteractive

if [ ! -f /usr/bin/createdb ]; then
	echo "Error: Missing PG createdb! First run ./installer/postgres.sh"; exit 1;
fi

if [ ! -d installer ]; then
	echo "Usage: ./installer/app-installer.sh"
	exit 1
fi

for opt in $@; do
	if [ "${opt}" == '--no-demo' ]; then
		WITH_DEMO='false'
	elif [ "${opt}" == '--no-qgis' ]; then
		WITH_QGIS='false'
	fi
done

# 1. Install packages (assume PG is preinstalled)
apt-get -y install apache2 libapache2-mod-php php-{pgsql,curl,mbstring,xml,zip} composer

if [ "${WITH_QGIS}" == 'true' ]; then
    apt-get -y install libapache2-mod-fcgid
fi

# manual check to avoid apt exit, if gdal is preinstalled from gdal-formats, package is on hold
if [ ! -f /usr/bin/ogr2ogr ]; then
	apt-get -y install gdal-bin
fi

# 2. Create db
su postgres <<CMD_EOF
createdb ${APP_DB}
createuser -sd ${APP_DB_USER}
psql -c "alter user ${APP_DB_USER} with password '${APP_DB_PASS}'"
psql -c "ALTER DATABASE ${APP_DB} OWNER TO ${APP_DB_USER}"

cd installer/database/
psql -d ${APP_DB} < schema.sql
psql -d ${APP_DB} < migrations/004_create_harvest_settings.sql
psql -d ${APP_DB} < migrations/005_create_users_and_roles.sql
psql -d ${APP_DB} < migrations/006_insert_keywords.sql
psql -d ${APP_DB} < migrations/007_insert_topics.sql
psql -d ${APP_DB} < migrations/008_add_thumbnail_to_gis_files.sql
CMD_EOF

echo "${APP_DB} pass: ${APP_DB_PASS}" >> /root/auth.txt

# install app
mkdir ${APP_DIR}
if [ "${WITH_QGIS}" == 'true' ]; then
    mkdir -p ${DATA_DIR}
    mkdir -p ${CACHE_DIR}
fi

cp -r . ${APP_DIR}/
chown -R www-data:www-data ${APP_DIR}
if [ "${WITH_QGIS}" == 'true' ]; then
    chown -R www-data:www-data ${DATA_DIR}
    chown -R www-data:www-data ${CACHE_DIR}
    
    rm -f ${APP_DIR}/public/index_no_qgis.php
    rm -f ${APP_DIR}templates/form_no_qgis.twig
else
    mv ${APP_DIR}/public/index_no_qgis.php ${APP_DIR}/public/index.php
    mv ${APP_DIR}/templates/form_no_qgis.twig ${APP_DIR}/templates/form.twig
fi

chmod -R 755 ${APP_DIR}
chmod -R 775 ${APP_DIR}/storage

if [ "${WITH_DEMO}" == 'true' ]; then
    
    if [ "${WITH_QGIS}" == 'false' ]; then
        # remove 2 QGIS project from demo
        sed -i.save '
/181dd2c6\-6d56\-4c91\-b550\-0b62ea6d4a68/d
/00ee1f11\-9808\-4895\-a2d2\-9612cd15df49/d
' installer/database/demo.sql
        rm -f installer/database/demo.sql.save
        
        rm -f installer/demo_uploads/686aec106b750_3.zip
        rm -f installer/demo_uploads/thumbnails/686aec106b2b3.png
    fi

    sed -i.save "
s|https://qgis-server|https://${HNAME}|
" installer/database/demo.sql

    su postgres <<CMD_EOF
cd installer/database/
psql -d ${APP_DB} < demo.sql
CMD_EOF
    cp -r installer/demo_uploads/* ${APP_DIR}/storage/uploads/
    chown -R www-data:www-data ${APP_DIR}/storage/uploads/
    
    if [ "${WITH_QGIS}" == 'true' ]; then
        mkdir -p ${DATA_DIR}/qgis/
        cp -r installer/qgs_data/* ${DATA_DIR}/qgis/
        chown -R www-data:www-data ${DATA_DIR}/qgis/
    fi
fi

rm -rf ${APP_DIR}/installer


pushd ${APP_DIR}
    sudo -u www-data composer install
    sudo -u www-data composer require slim/twig-view
    sudo -u www-data composer require tecnickcom/tcpdf
popd

sed "
s|\$APP_DIR|$APP_DIR|
s|\$HNAME|$HNAME|
" < installer/apache2.conf >/etc/apache2/sites-available/000-default.conf

a2enmod ssl rewrite headers
if [ "${WITH_QGIS}" == 'true' ]; then
    a2enmod proxy_http
else
    sed -i.save '/ProxyPass/d' /etc/apache2/sites-available/000-default.conf
    rm -f /etc/apache2/sites-available/000-default.conf.save
fi
systemctl restart apache2

cat >${APP_DIR}/.env <<CAT_EOF
DB_HOST=localhost
DB_PORT=5432
DB_NAME=${APP_DB}
DB_USER=${APP_DB_USER}
DB_PASS=${APP_DB_PASS}
APP_ENV=production
APP_DEBUG=false
APP_URL=http://${HNAME}
CAT_EOF
chown www-data:www-data ${APP_DIR}/.env
chmod 440 ${APP_DIR}/.env

# install QGIS
if [ "${WITH_QGIS}" == 'true' ]; then
    install_qgis_server
    install_qgis_server_plugins

    sed "s|\$DATA_DIR|$DATA_DIR|
s|\$APP_DIR|$APP_DIR|
s|\$CACHE_DIR|$CACHE_DIR|
s|\$HNAME|$HNAME|
s|QGIS_SERVER_MAX_THREADS 2|QGIS_SERVER_MAX_THREADS ${NUM_PROCS}|
" < installer/qgis_apache2.conf > /etc/apache2/sites-available/qgis.conf

    echo 'Listen 127.0.0.1:8001' >> /etc/apache2/ports.conf

    a2enmod headers expires fcgid cgi rewrite
    a2ensite qgis
    a2disconf serve-cgi-bin
    
    systemctl restart apache2
fi

apt-get -y clean all
