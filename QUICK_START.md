# 🚀 Quick Start - Display Project in Browser

This is the fastest way to get Leader Data Service running in your browser.

## ⚡ 3-Step Quick Start

### Step 1️⃣: Navigate to Backend Directory

```bash
cd Leader_data_service/backend
```

### Step 2️⃣: Install Dependencies & Setup

```bash
# Install PHP dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
php artisan migrate
php artisan db:seed
```

### Step 3️⃣: Start Server & Open Browser

```bash
# Start the Laravel server
php artisan serve
```

**Then open your browser and go to:**

```
http://localhost:8000
```

🎉 **That's it!** You should now see the File Manager interface.

---

## 🌐 What You'll See

When you open `http://localhost:8000` in your browser, you'll see:

- **File Manager Dashboard** - The main interface
- **Upload Area** - Drag & drop or browse to upload files
- **Storage Statistics** - Your current storage usage
- **File List** - All uploaded files with download/delete options
- **Search Box** - Find files quickly

---

## 📱 Available Pages

Once the server is running, you can access these pages:

| Page | URL | What it does |
|------|-----|--------------|
| File Manager | http://localhost:8000/ | Main dashboard (upload, manage files) |
| Welcome | http://localhost:8000/welcome | Welcome page with info |
| Login | http://localhost:8000/login | Login interface |
| Register | http://localhost:8000/register | User registration |

---

## 🛠️ Troubleshooting

### Server won't start?

**Error: "Address already in use"**
```bash
# Use a different port
php artisan serve --port=8080
# Then access: http://localhost:8080
```

**Error: "composer: command not found"**
```bash
# Install Composer first
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

**Error: Missing PHP extensions**
```bash
# Ubuntu/Debian
sudo apt install php8.3-mysql php8.3-xml php8.3-mbstring php8.3-curl

# macOS (with Homebrew)
brew install php@8.3
```

### Browser shows blank page?

1. Check the terminal where server is running for errors
2. Try clearing cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```
3. Make sure you're using `http://localhost:8000` (not https)

### Port 8000 already in use?

Check what's using port 8000:
```bash
# On Linux/Mac
lsof -i :8000

# On Windows
netstat -ano | findstr :8000
```

Then either stop that process or use a different port:
```bash
php artisan serve --port=8001
```

---

## 📚 Need More Help?

- **Detailed Browser Guide**: [BROWSER_GUIDE.md](BROWSER_GUIDE.md)
- **Full Setup Documentation**: [README.md](README.md)
- **Server Deployment**: [DEPLOYMENT.md](DEPLOYMENT.md)

---

## 🔄 Stopping the Server

To stop the server:
1. Go to the terminal where `php artisan serve` is running
2. Press `Ctrl + C`

---

## ⚠️ Important Notes

- **Keep the terminal open** while using the application
- **First-time setup** takes a few minutes for `composer install`
- **Storage location**: Uploaded files go to `backend/storage/app/public/uploads`
- **Database**: Uses SQLite by default (no extra database setup needed)

---

## 🎯 Common Use Cases

### Just want to see the interface?
```bash
php artisan serve
# Then open http://localhost:8000
```

### Want to test file uploads?
1. Start server: `php artisan serve`
2. Open: http://localhost:8000
3. Drag & drop any supported file
4. See it appear in the file list

### Want to use the API?
```bash
# Server must be running
php artisan serve

# In another terminal, test API
curl http://localhost:8000/api/files
```

---

**Ready to go? Run these commands:**

```bash
cd Leader_data_service/backend
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan serve
```

**Then open:** http://localhost:8000

🎉 **Enjoy!**
