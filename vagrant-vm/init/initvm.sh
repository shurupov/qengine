#!/bin/bash

#Variables
GITHUB_ACCESS_TOKEN=c6289bbe7d5a39dbfffcfd3c0ad3905b83252226 #Это надо для yii. Потом переметим куда надо
PROJECT_NAME=qe
SOURCES_PATH=$(pwd)

INSTALL_PHP=true
INSTALL_NODEJS=true
INSTALL_MYSQL=false
INSTALL_MONGO=true
INSTALL_APACHE=true

INSTALL_ADMINMONGO=true
INSTALL_PHPMYADMIN=false
INSTALL_ROCKMONGO=false
INSTALL_PHPMONGODB=false

INSTALL_PROJECT=true

sudo apt update -q
sudo apt autoremove -y
sudo apt upgrade -y

sudo apt install mc git wget curl -y

if $INSTALL_MONGO; then
    #Устнановка Mongo
    sudo apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv 0C49F3730359A14518585931BC711F9BA15703C6
    echo "deb [ arch=amd64,arm64 ] http://repo.mongodb.org/apt/ubuntu xenial/mongodb-org/3.4 multiverse" | sudo tee /etc/apt/sources.list.d/mongodb-org-3.4.list

    sudo apt update -q
    sudo apt upgrade -y

    sudo apt install -y mongodb-org

    sudo service mongod start
    sudo systemctl enable mongod
fi

if $INSTALL_NODEJS; then
    sudo apt install curl pkg-config libssl-dev libsslcommon2-dev -y
    curl -sL https://deb.nodesource.com/setup_6.x | sudo bash -e -
    sudo apt install -y nodejs git
fi

if $INSTALL_ADMINMONGO && $INSTALL_MONGO && $INSTALL_NODEJS; then
    sudo mkdir /adminmongo
    sudo chmod 777 /adminmongo
    cd /adminmongo
    git clone https://github.com/mrvautin/adminMongo.git ./
    sudo npm install npm -g
    sudo npm install
    sudo npm start &
    sudo sed -i "s/^exit 0$/cd \/adminmongo\nnpm start \&\nexit\ 0/g" /etc/rc.local
fi

if $INSTALL_ROCKMONGO && $INSTALL_MONGO && $INSTALL_APACHE && $INSTALL_PHP; then
    sudo mkdir /var/log/rockmongo
    sudo git clone https://github.com/krutpong/rockmongo-php7 /rockmongo
    sudo cp vhosts/rockmongo.conf /etc/apache2/sites-available
    sudo ln -s /etc/apache2/sites-available/rockmongo.conf /etc/apache2/sites-enabled
fi

if $INSTALL_PHPMONGODB && $INSTALL_MONGO && $INSTALL_APACHE && $INSTALL_PHP; then
    sudo mkdir /var/log/phpmongodb
    sudo git clone https://github.com/phpmongodb/phpmongodb.git /phpmongodb
    sudo cp vhosts/phpmongodb.conf /etc/apache2/sites-available
    sudo ln -s /etc/apache2/sites-available/phpmongodb.conf /etc/apache2/sites-enabled
fi

if $INSTALL_MYSQL; then
    #install mysql
    DBPASSWD=12345
    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password password $DBPASSWD"
    sudo debconf-set-selections <<< "mysql-server mysql-server/root_password_again password $DBPASSWD"
    sudo apt install -y mysql-server
    echo "Start installing db data"
    cd $SOURCES_PATH
    cd db
    dirs=( $(ls) )
    for dir in ${dirs[@]}; do
        echo $dir
        cd $dir
        files=( $(ls) )
        for file in ${files[@]}; do
            sudo mysql --password=$DBPASSWD < $file
            echo Dump db/$dir/$file applied
        done
        cd ..
    done
    cd ..
fi

if $INSTALL_APACHE; then
    sudo apt install apache2 -y
    sudo rm /etc/apache2/sites-enabled/*
    cd $SOURCES_PATH
    sudo cp vhosts/project.conf /etc/apache2/sites-available
    sudo ln -s /etc/apache2/sites-available/project.conf /etc/apache2/sites-enabled
fi

if $INSTALL_PHP; then
    sudo apt install php-cli php-common php-json php-mysql php-mbstring php-gd php-curl php-zip libapache2-mod-php php-xml php-intl php-mongodb wget curl -y -f
fi

if $INSTALL_PHP && $INSTALL_APACHE; then
    sudo apt install libapache2-mod-php -y
fi

if $INSTALL_PHP && $INSTALL_MYSQL; then
    sudo apt-get install php-mysql php-mbstring php-gd php-curl php-zip libapache2-mod-php php-xml php-intl php-mongodb wget curl -y
fi

#if $INSTALL_PHP && $INSTALL_MONGO; then
#    sudo apt install php-dev pkg-config libssl-dev libsslcommon2-dev php-pear -y
#    sudo pecl install mongodb
#fi

if $INSTALL_PHPMYADMIN && $INSTALL_PHP && $INSTALL_APACHE && $INSTALL_MYSQL; then
    echo "Loading phpmyadmin from http://downloads.sourceforge.net/project/phpmyadmin/phpMyAdmin/4.3.0/phpMyAdmin-4.3.0-all-languages.tar.gz"
    echo "This may take a few minutes..."
    wget -q http://files.phpmyadmin.net/phpMyAdmin/4.4.11/phpMyAdmin-4.4.11-all-languages.tar.gz
    echo "Loading phpmyadmin completed"
    sudo mkdir /pma
    sudo mkdir /var/log/pma
    sudo tar -xf phpMyAdmin-4.4.11-all-languages.tar.gz -C /pma
    rm phpMyAdmin-4.4.11-all-languages.tar.gz
    sudo mv /pma/phpMyAdmin-4.4.11-all-languages /pma/www
    cd $SOURCES_PATH
    sudo cp vhosts/pma.conf /etc/apache2/sites-available
    sudo ln -s /etc/apache2/sites-available/pma.conf /etc/apache2/sites-enabled
fi

if $INSTALL_PROJECT && $INSTALL_PHP && $INSTALL_APACHE; then
    cd
    curl -sS https://getcomposer.org/installer | php
    sudo mv composer.phar /usr/local/bin/composer
    # Проверяем vagrant это или сервер
    if ! [ -d /project/ ]; then #Если сервер
        ln -s /$PROJECT_NAME/project /project
        chmod 777 -R /$PROJECT_NAME/project/
        chmod 777 -R /$PROJECT_NAME/project/*
        chmod 777 -R /project/
        sudo sed -i "s/app_dev.php/app.php/g" /etc/apache2/sites-enabled/project.conf
        sudo sed -i "s/upload_max_filesize = .*?\n/upload_max_filesize = 100M\n/g" /etc/php/7.0/apache2/php.ini
    else #Если vagrant
        sudo sed -i "s/www-data/ubuntu/g" /etc/apache2/envvars
    fi

    cd /project
    composer install --no-interaction
#    cp /project/app/config/parameters.yml.dist /project/app/config/parameters.yml

    sudo mkdir /var/log/project

    sudo a2enmod rewrite
    sudo service apache2 restart
fi

echo "$PROJECT_NAME server successfully installed!"
echo "Congratulations!"
echo "Сервер $PROJECT_NAME успешно установлен!"
echo "Ура!"
