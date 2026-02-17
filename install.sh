#!/bin/bash

# Leader Data Service - Quick Installation Script for Ubuntu
# This script automates the installation process on Ubuntu 22.04/24.04

set -e  # Exit on error

echo "=================================================="
echo "Leader Data Service - Installation Script"
echo "=================================================="
echo ""

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo "Please run as root or with sudo"
    exit 1
fi

# Color codes
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

echo -e "${GREEN}[1/9] Updating system...${NC}"
apt update && apt upgrade -y

echo -e "${GREEN}[2/9] Installing PHP 8.3 and extensions...${NC}"
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl \
    php8.3-gd php8.3-zip php8.3-sqlite3

echo -e "${GREEN}[3/9] Installing Nginx...${NC}"
apt install -y nginx

echo -e "${GREEN}[4/9] Installing MySQL...${NC}"
apt install -y mysql-server

echo -e "${GREEN}[5/9] Installing Composer...${NC}"
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo -e "${GREEN}[6/9] Installing Git...${NC}"
apt install -y git unzip

echo ""
echo -e "${YELLOW}=== Database Setup ===${NC}"
echo "Please enter database details:"
read -p "Database name [leader_data_service]: " DB_NAME
DB_NAME=${DB_NAME:-leader_data_service}

read -p "Database user [leader_user]: " DB_USER
DB_USER=${DB_USER:-leader_user}

read -sp "Database password: " DB_PASS
echo ""

if [ -z "$DB_PASS" ]; then
    echo -e "${RED}Error: Password cannot be empty${NC}"
    exit 1
fi

echo -e "${GREEN}[7/9] Creating database and user...${NC}"
mysql -u root <<MYSQL_SCRIPT
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
MYSQL_SCRIPT

echo ""
read -p "Installation directory [/var/www/Leader_data_service]: " INSTALL_DIR
INSTALL_DIR=${INSTALL_DIR:-/var/www/Leader_data_service}

echo -e "${GREEN}[8/9] Cloning application...${NC}"
mkdir -p $(dirname $INSTALL_DIR)
cd $(dirname $INSTALL_DIR)

if [ -d "$INSTALL_DIR" ]; then
    echo -e "${YELLOW}Directory exists. Pulling latest changes...${NC}"
    cd $INSTALL_DIR
    git pull
else
    git clone https://github.com/dokrak/Leader_data_service.git $(basename $INSTALL_DIR)
    cd $INSTALL_DIR
fi

cd backend

echo -e "${GREEN}Installing PHP dependencies...${NC}"
composer install --optimize-autoloader --no-dev

echo -e "${GREEN}Configuring environment...${NC}"
cp .env.example .env

# Update .env file
sed -i "s/APP_ENV=.*/APP_ENV=production/" .env
sed -i "s/APP_DEBUG=.*/APP_DEBUG=false/" .env
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

php artisan key:generate --force

echo -e "${GREEN}Running migrations...${NC}"
php artisan migrate --force

echo -e "${GREEN}Seeding database...${NC}"
php artisan db:seed --force

echo -e "${GREEN}Setting up storage...${NC}"
php artisan storage:link

echo -e "${GREEN}[9/9] Setting permissions...${NC}"
chown -R www-data:www-data $INSTALL_DIR
chmod -R 755 $INSTALL_DIR
chmod -R 775 $INSTALL_DIR/backend/storage
chmod -R 775 $INSTALL_DIR/backend/bootstrap/cache

echo ""
echo -e "${GREEN}=== Installation Complete! ===${NC}"
echo ""
echo "Next steps:"
echo "1. Configure Nginx (see DEPLOYMENT.md)"
echo "2. Setup SSL certificate with Let's Encrypt"
echo "3. Configure firewall"
echo "4. Setup automatic backups"
echo ""
echo "Application location: $INSTALL_DIR/backend"
echo ""
read -p "Do you want to see the Nginx configuration template? (y/n): " SHOW_NGINX

if [ "$SHOW_NGINX" = "y" ]; then
    cat <<'NGINX_CONFIG'

=== Nginx Configuration Template ===

Create file: /etc/nginx/sites-available/leader-data-service

server {
    listen 80;
    server_name your-domain.com;
    
    root /var/www/Leader_data_service/backend/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

Then run:
sudo ln -s /etc/nginx/sites-available/leader-data-service /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx

For SSL: sudo certbot --nginx -d your-domain.com

NGINX_CONFIG
fi

echo ""
echo -e "${GREEN}Done! See DEPLOYMENT.md for complete documentation.${NC}"
