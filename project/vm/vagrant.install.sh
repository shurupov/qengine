#!/bin/bash

export OS_TYPE="ubuntu1604"
cd /var/www/html/project
export SETTINGS=$(cat vm/settings.json)

sudo chmod 777 -R /install
cd /install
chmod +x ./vagrant.install.sh
./vagrant.install.sh
