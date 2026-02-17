# 🏥 Hospital Server Deployment Guide

## System Requirements

### Minimum Server Specifications
- **OS**: Ubuntu 22.04 LTS / Ubuntu 24.04 LTS (recommended) or CentOS/RHEL 8+
- **CPU**: 2+ cores
- **RAM**: 4GB minimum, 8GB recommended
- **Storage**: 50GB+ (depending on file storage needs)
- **PHP**: 8.2 or 8.3
- **Database**: MySQL 8.0+ / MariaDB 10.3+ / PostgreSQL 13+
- **Web Server**: Nginx (recommended) or Apache 2.4+

### Required PHP Extensions
```
php-cli
php-fpm
php-mysql (or php-pgsql for PostgreSQL)
php-mbstring
php-xml
php-bcmath
php-curl
php-gd
php-zip
php-sqlite3
```

---

## 📋 Pre-Installation Checklist

- [ ] Server access (SSH)
- [ ] Domain name or IP address
- [ ] SSL certificate (Let's Encrypt recommended)
- [ ] Database credentials
- [ ] Backup storage location configured

---

## 🚀 Installation Steps

### Step 1: Update System and Install Dependencies

**For Ubuntu/Debian:**
```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install required packages
sudo apt install -y software-properties-common

# Add PHP repository
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 and extensions
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-curl \
    php8.3-gd php8.3-zip php8.3-sqlite3

# Install Nginx
sudo apt install -y nginx

# Install MySQL
sudo apt install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Git
sudo apt install -y git unzip
```

**For CentOS/RHEL:**
```bash
# Update system
sudo dnf update -y

# Install EPEL and Remi repository
sudo dnf install -y epel-release
sudo dnf install -y https://rpms.remirepo.net/enterprise/remi-release-8.rpm

# Enable PHP 8.3
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.3 -y

# Install PHP and extensions
sudo dnf install -y php php-fpm php-cli php-mysqlnd \
    php-mbstring php-xml php-bcmath php-curl php-gd php-zip

# Install Nginx
sudo dnf install -y nginx

# Install MySQL
sudo dnf install -y mysql-server

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Git
sudo dnf install -y git unzip
```

---

### Step 2: Setup MySQL Database

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Login to MySQL
sudo mysql -u root -p

# Create database and user
CREATE DATABASE leader_data_service;
CREATE USER 'leader_user'@'localhost' IDENTIFIED BY 'STRONG_PASSWORD_HERE';
GRANT ALL PRIVILEGES ON leader_data_service.* TO 'leader_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

**⚠️ Important:** Replace `STRONG_PASSWORD_HERE` with a strong password!

---

### Step 3: Clone and Setup Application

```bash
# Create application directory
sudo mkdir -p /var/www
cd /var/www

# Clone repository
sudo git clone https://github.com/dokrak/Leader_data_service.git
sudo chown -R $USER:$USER Leader_data_service
cd Leader_data_service/backend

# Install PHP dependencies
composer install --optimize-autoloader --no-dev

# Create environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

---

### Step 4: Configure Environment

Edit the `.env` file:

```bash
nano .env
```

**Critical Configuration:**

```env
APP_NAME="Leader Data Service"
APP_ENV=production
APP_KEY=base64:YOUR_GENERATED_KEY
APP_DEBUG=false
APP_URL=https://your-domain.com

# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leader_data_service
DB_USERNAME=leader_user
DB_PASSWORD=STRONG_PASSWORD_HERE

# File Storage
FILESYSTEM_DISK=public

# Session & Cache
SESSION_DRIVER=file
CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# Logging
LOG_CHANNEL=daily
LOG_LEVEL=error
```

**🔒 Security Settings:**
- Set `APP_DEBUG=false` in production
- Use strong, unique passwords
- Never commit `.env` to version control

---

### Step 5: Setup Database and Storage

```bash
# Run migrations
php artisan migrate --force

# Seed initial data (storage quota)
php artisan db:seed --force

# Setup storage permissions
php artisan storage:link

# Set correct permissions
sudo chown -R www-data:www-data /var/www/Leader_data_service
sudo chmod -R 755 /var/www/Leader_data_service
sudo chmod -R 775 /var/www/Leader_data_service/backend/storage
sudo chmod -R 775 /var/www/Leader_data_service/backend/bootstrap/cache
```

---

### Step 6: Configure Nginx

Create Nginx configuration:

```bash
sudo nano /etc/nginx/sites-available/leader-data-service
```

**Nginx Configuration:**

```nginx
server {
    listen 80;
    server_name your-domain.com www.your-domain.com;
    
    # Redirect HTTP to HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name your-domain.com www.your-domain.com;
    
    root /var/www/Leader_data_service/backend/public;
    index index.php index.html;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/your-domain.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/your-domain.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers HIGH:!aNULL:!MD5;
    ssl_prefer_server_ciphers on;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:;" always;

    # Max upload size (adjust as needed)
    client_max_body_size 100M;

    # Logging
    access_log /var/log/nginx/leader-data-service-access.log;
    error_log /var/log/nginx/leader-data-service-error.log;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 256 16k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    location ~* \.(jpg|jpeg|png|gif|ico|css|js|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }
}
```

**Enable the site:**

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/leader-data-service /etc/nginx/sites-enabled/

# Remove default site
sudo rm /etc/nginx/sites-enabled/default

# Test Nginx configuration
sudo nginx -t

# Restart Nginx
sudo systemctl restart nginx

# Enable Nginx on boot
sudo systemctl enable nginx
```

---

### Step 7: Install SSL Certificate (Let's Encrypt)

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d your-domain.com -d www.your-domain.com

# Auto-renewal is configured automatically
# Test renewal
sudo certbot renew --dry-run
```

---

### Step 8: Configure Firewall

```bash
# Install UFW (if not installed)
sudo apt install -y ufw

# Allow SSH
sudo ufw allow 22/tcp

# Allow HTTP and HTTPS
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp

# Enable firewall
sudo ufw enable
```

---

## 🔒 Security Hardening (Hospital Environment)

### 1. Disable Directory Listing
Already configured in Nginx config above.

### 2. Setup Automatic Security Updates
```bash
sudo apt install -y unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

### 3. Configure Fail2Ban (Protect against brute force)
```bash
sudo apt install -y fail2ban

# Create custom jail configuration
sudo nano /etc/fail2ban/jail.local
```

Add:
```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[sshd]
enabled = true

[nginx-limit-req]
enabled = true
filter = nginx-limit-req
logpath = /var/log/nginx/*error.log
```

```bash
sudo systemctl enable fail2ban
sudo systemctl start fail2ban
```

### 4. Regular Backups

**Database Backup Script:**
```bash
sudo nano /usr/local/bin/backup-leader-data.sh
```

```bash
#!/bin/bash

BACKUP_DIR="/backups/leader-data-service"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="leader_data_service"
DB_USER="leader_user"
DB_PASS="YOUR_PASSWORD"

# Create backup directory
mkdir -p $BACKUP_DIR

# Backup database
mysqldump -u $DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/db_$DATE.sql

# Backup uploaded files
tar -czf $BACKUP_DIR/files_$DATE.tar.gz /var/www/Leader_data_service/backend/storage/app/public

# Delete backups older than 30 days
find $BACKUP_DIR -type f -mtime +30 -delete

echo "Backup completed: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-leader-data.sh

# Schedule daily backup at 2 AM
sudo crontab -e
```

Add:
```
0 2 * * * /usr/local/bin/backup-leader-data.sh >> /var/log/backup-leader-data.log 2>&1
```

### 5. Setup Monitoring

**Install System Monitoring:**
```bash
sudo apt install -y htop iotop nethogs
```

**Monitor Application Logs:**
```bash
# Application logs
tail -f /var/www/Leader_data_service/backend/storage/logs/laravel.log

# Nginx logs
tail -f /var/log/nginx/leader-data-service-error.log
```

---

## 🔧 Maintenance

### Update Application
```bash
cd /var/www/Leader_data_service

# Pull latest changes
git pull origin main

# Update dependencies
cd backend
composer install --optimize-autoloader --no-dev

# Run migrations
php artisan migrate --force

# Clear cache
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Restart PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Monitor Disk Space
```bash
# Check disk usage
df -h

# Check storage usage
du -sh /var/www/Leader_data_service/backend/storage/app/public/uploads
```

### Clear Old Logs
```bash
# Clear Laravel logs older than 7 days
find /var/www/Leader_data_service/backend/storage/logs -name "*.log" -type f -mtime +7 -delete
```

---

## 📊 Adjust Storage Quota

```bash
cd /var/www/Leader_data_service/backend
php artisan tinker
```

```php
// Change to 50GB
$quota = App\Models\StorageQuota::first();
$quota->update(['total_quota' => 53687091200]);
exit
```

---

## 🆘 Troubleshooting

### Check Logs
```bash
# Application errors
tail -f /var/www/Leader_data_service/backend/storage/logs/laravel.log

# Nginx errors
tail -f /var/log/nginx/leader-data-service-error.log

# PHP-FPM errors
tail -f /var/log/php8.3-fpm.log
```

### Permission Issues
```bash
sudo chown -R www-data:www-data /var/www/Leader_data_service
sudo chmod -R 775 /var/www/Leader_data_service/backend/storage
sudo chmod -R 775 /var/www/Leader_data_service/backend/bootstrap/cache
```

### Database Connection Issues
```bash
# Test database connection
cd /var/www/Leader_data_service/backend
php artisan tinker
DB::connection()->getPdo();
exit
```

---

## 📞 Support Contacts

- **Documentation**: https://github.com/dokrak/Leader_data_service
- **Laravel Docs**: https://laravel.com/docs

---

## ✅ Post-Installation Checklist

- [ ] SSL certificate installed and auto-renewal working
- [ ] Firewall configured
- [ ] Automatic backups scheduled
- [ ] Application accessible via HTTPS
- [ ] File upload working
- [ ] Storage quota configured appropriately
- [ ] Monitoring setup
- [ ] Security updates enabled
- [ ] Users can access and upload files
- [ ] Download functionality working
- [ ] Documentation provided to users

---

**🎉 Your Leader Data Service is now deployed and ready for hospital use!**
