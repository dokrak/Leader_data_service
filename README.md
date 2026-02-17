# 📁 Leader Data Service

A file management system for collecting and managing Lead Team data and documents.

![Laravel](https://img.shields.io/badge/Laravel-11-FF2D20?logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3-777BB4?logo=php)
![License](https://img.shields.io/badge/License-MIT-green)

## 🎯 Want to Display the Project in Browser?

**→ [Quick Start Guide (3 Steps)](QUICK_START.md)** - Get up and running in minutes!

**→ [Browser Access Guide](BROWSER_GUIDE.md)** - Detailed instructions and troubleshooting

## ✨ Features

- 📤 **File Upload** - Drag & drop or browse to upload files
- 📊 **Storage Management** - Track storage quota and usage (10GB default)
- 🔍 **Search & Filter** - Find files by name or category
- ⬇️ **Download** - Easy file downloads
- 🗑️ **Delete** - Manage uploaded files
- 📱 **Responsive Design** - Works on desktop and mobile
- 🔒 **File Type Validation** - Supports office files and images
- 💾 **Storage Stats** - Real-time storage statistics

## 🚀 Quick Start

> 🌐 **Want to display in browser?** See [QUICK_START.md](QUICK_START.md) for a 3-step guide, or [BROWSER_GUIDE.md](BROWSER_GUIDE.md) for detailed instructions.

### Option 1: GitHub Codespaces (Fastest)

[![Open in GitHub Codespaces](https://github.com/codespaces/badge.svg)](https://codespaces.new/dokrak/Leader_data_service)

1. Click the badge above
2. Wait for the Codespace to build
3. The server will start automatically
4. Access the app at the forwarded port 8000

### Option 2: Local Development

**Prerequisites:**
- PHP 8.3+
- Composer
- SQLite (or MySQL/PostgreSQL)

**Setup:**
```bash
# Clone the repository
git clone https://github.com/dokrak/Leader_data_service.git
cd Leader_data_service/backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run migrations
php artisan migrate

# Seed storage quota
php artisan db:seed

# Start the server
php artisan serve
```

**Access in Browser:** Open `http://localhost:8000` in your web browser

📖 **Detailed browser instructions:** [BROWSER_GUIDE.md](BROWSER_GUIDE.md)

## 📋 API Endpoints

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/files` | List all files (with pagination) |
| POST | `/api/files` | Upload a new file |
| GET | `/api/files/{id}` | Get file details |
| GET | `/api/files/{id}/download` | Download file |
| PUT | `/api/files/{id}` | Update file details |
| DELETE | `/api/files/{id}` | Delete file |
| GET | `/api/storage/stats` | Get storage statistics |

## 📸 Screenshots

### File Manager Interface
The web interface provides an intuitive dashboard for managing files with:
- Real-time storage statistics
- Drag & drop upload
- File search and filtering
- One-click download and delete

## 🧪 Testing

Run the test suite:
```bash
cd backend
php artisan test
```

Run API tests:
```bash
# Automated tests
php artisan test --filter FileApiTest

# Manual API testing script
chmod +x ../test-api.sh
../test-api.sh
```

## 🔧 Configuration

### Storage Quota
The default storage quota is 10GB. To change it:

```bash
php artisan tinker
```
```php
$quota = App\Models\StorageQuota::first();
$quota->update(['total_quota' => 21474836480]); // 20GB
```

### Allowed File Types
Edit `app/Http/Controllers/FileController.php` to modify allowed file types:
```php
private const ALLOWED_TYPES = [
    'application/pdf',
    'application/msword',
    // Add more types...
];
```

## 📁 Project Structure

```
Leader_data_service/
├── backend/
│   ├── app/
│   │   ├── Http/Controllers/
│   │   │   └── FileController.php
│   │   └── Models/
│   │       ├── UploadedFile.php
│   │       └── StorageQuota.php
│   ├── database/
│   │   └── migrations/
│   ├── resources/
│   │   └── views/
│   │       └── file-manager.blade.php
│   ├── routes/
│   │   ├── api.php
│   │   └── web.php
│   └── tests/
└── test-api.sh
```

## 🌐 Live Demo

**Demo URL:** `https://sturdy-space-robot-xrj9wxgp6v7265gg-8000.app.github.dev/`

*(Available when developer's Codespace is running)*

## 📚 Documentation

- **[Browser Access Guide](BROWSER_GUIDE.md)** - How to access and use the application in your browser
- **[Deployment Guide](DEPLOYMENT.md)** - Production server deployment instructions
- **[New Features](NEW_FEATURES.md)** - Latest features and updates

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📝 License

This project is open-sourced under the MIT License.

## 👥 Team

Developed for the Leader Team data collection and management.

## 📧 Support

For issues or questions, please open an issue on GitHub.

---

**Made with ❤️ using Laravel**