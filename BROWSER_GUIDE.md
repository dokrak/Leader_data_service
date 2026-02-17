# 🌐 Browser Access Guide - Leader Data Service

This guide explains how to access and use the Leader Data Service through your web browser.

---

## 📋 Table of Contents

1. [Quick Start (Local Development)](#quick-start-local-development)
2. [Accessing the Application](#accessing-the-application)
3. [Available Pages](#available-pages)
4. [Using the File Manager Interface](#using-the-file-manager-interface)
5. [Browser Requirements](#browser-requirements)
6. [Troubleshooting](#troubleshooting)

---

## 🚀 Quick Start (Local Development)

### Prerequisites
- PHP 8.3 or higher installed
- Composer installed
- Web browser (Chrome, Firefox, Safari, or Edge)

### Step 1: Start the Application

Open a terminal and navigate to the project directory:

```bash
cd Leader_data_service/backend
```

Start the Laravel development server:

```bash
php artisan serve
```

You should see output similar to:
```
INFO  Server running on [http://127.0.0.1:8000]
```

### Step 2: Open Your Browser

Open your preferred web browser and navigate to:

```
http://localhost:8000
```

**Alternative addresses that work:**
- `http://127.0.0.1:8000`
- `http://0.0.0.0:8000` (if configured)

> 💡 **Tip**: Keep the terminal window open while using the application. Closing it will stop the server.

---

## 🌍 Accessing the Application

### Local Development URLs

| Page | URL | Description |
|------|-----|-------------|
| **File Manager** | `http://localhost:8000/` | Main dashboard for managing files |
| **Welcome Page** | `http://localhost:8000/welcome` | Application welcome screen |
| **Login** | `http://localhost:8000/login` | User login page |
| **Register** | `http://localhost:8000/register` | User registration page |

### Production/Server Deployment URLs

If deployed on a server, replace `localhost:8000` with your domain:

- `https://your-domain.com/`
- `https://your-server-ip/`

> 🔒 **Security Note**: Production deployments should always use HTTPS (not HTTP).

---

## 📄 Available Pages

### 1. File Manager (Main Dashboard)
**URL:** `http://localhost:8000/`

The main interface where you can:
- 📤 Upload files (drag & drop or browse)
- 📊 View storage statistics
- 🔍 Search and filter files
- ⬇️ Download files
- 🗑️ Delete files

### 2. Welcome Page
**URL:** `http://localhost:8000/welcome`

Landing page with:
- System information
- Feature highlights
- Getting started guide

### 3. Login Page
**URL:** `http://localhost:8000/login`

User authentication interface (if authentication is enabled).

### 4. Register Page
**URL:** `http://localhost:8000/register`

New user registration (if authentication is enabled).

---

## 🎯 Using the File Manager Interface

### Uploading Files

**Method 1: Drag & Drop**
1. Open `http://localhost:8000/` in your browser
2. Drag files from your computer
3. Drop them onto the upload area
4. Wait for the upload to complete

**Method 2: Browse**
1. Click the "Browse Files" or upload button
2. Select files from your computer
3. Click "Open" or "Upload"
4. Wait for confirmation

### Supported File Types
- 📄 Documents: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX
- 🖼️ Images: JPG, JPEG, PNG, GIF
- 📁 Others: TXT, CSV, ZIP

### Managing Files

**Search Files:**
- Use the search box at the top
- Type filename or keywords
- Results filter automatically

**Download Files:**
- Click the download icon next to any file
- File will download to your browser's download folder

**Delete Files:**
- Click the delete/trash icon next to any file
- Confirm the deletion when prompted

**View Storage Stats:**
- Storage usage displayed at the top of the dashboard
- Shows used space / total available space
- Visual progress bar indicates usage percentage

---

## 💻 Browser Requirements

### Recommended Browsers

✅ **Fully Supported:**
- Google Chrome 90+
- Mozilla Firefox 88+
- Microsoft Edge 90+
- Safari 14+
- Opera 76+

### Minimum Requirements
- JavaScript enabled
- Cookies enabled
- Modern HTML5 support
- File upload API support

### Screen Resolutions
- Desktop: 1024×768 or higher
- Tablet: 768×1024
- Mobile: 375×667 or higher

> 📱 The interface is fully responsive and works on all screen sizes.

---

## 🔧 Troubleshooting

### Issue: "This site can't be reached" or "Connection refused"

**Solution:**
1. Make sure the server is running:
   ```bash
   cd backend
   php artisan serve
   ```
2. Check that you're using the correct URL: `http://localhost:8000`
3. Check if port 8000 is available (not used by another application)
4. Try using `http://127.0.0.1:8000` instead

### Issue: "404 Not Found" on certain pages

**Solution:**
1. Make sure you're accessing the correct URL
2. Check that routes are properly configured in `routes/web.php`
3. Clear the route cache:
   ```bash
   php artisan route:clear
   ```

### Issue: Files won't upload

**Solution:**
1. Check file size (max size may be limited)
2. Verify file type is supported
3. Check storage permissions:
   ```bash
   chmod -R 775 storage
   ```
4. Ensure storage is linked:
   ```bash
   php artisan storage:link
   ```
5. Check available disk space

### Issue: Blank white page

**Solution:**
1. Check Laravel logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```
2. Enable debug mode temporarily (in `.env`):
   ```
   APP_DEBUG=true
   ```
3. Clear application cache:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan view:clear
   ```

### Issue: Page loads but looks broken (missing styles)

**Solution:**
1. Check browser console for errors (F12 → Console tab)
2. Clear browser cache (Ctrl+Shift+Delete)
3. Try incognito/private browsing mode
4. Ensure assets are properly compiled:
   ```bash
   npm install
   npm run build
   ```

### Issue: Different port needed

**Solution:**
Start server on a different port:
```bash
php artisan serve --port=8080
```

Then access at: `http://localhost:8080`

---

## 🔐 Production Access

When deployed on a production server:

1. **Get your server URL from your administrator**
   - Example: `https://data.hospital.com`
   - Or IP: `https://192.168.1.100`

2. **Open the URL in your browser**
   - Production sites should use HTTPS (secure)
   - You may see a security certificate warning if using self-signed certs

3. **Bookmark for easy access**
   - Save the URL in your browser bookmarks
   - Add to home screen on mobile devices

---

## 📚 Additional Resources

- **Main README**: [README.md](README.md) - Installation and setup
- **Deployment Guide**: [DEPLOYMENT.md](DEPLOYMENT.md) - Server deployment
- **API Documentation**: See README.md for API endpoint details

---

## ✨ Tips for Best Experience

1. **Use a modern browser** - Chrome or Firefox recommended
2. **Stable internet connection** - Required for file uploads/downloads
3. **Adequate bandwidth** - For large file transfers
4. **Keep browser updated** - For security and compatibility
5. **Enable cookies** - Required for session management
6. **Allow pop-ups** (if needed) - For downloads in some browsers

---

## 🆘 Need Help?

If you encounter issues not covered in this guide:

1. Check the [README.md](README.md) for general documentation
2. Review [DEPLOYMENT.md](DEPLOYMENT.md) for server-specific issues
3. Open an issue on [GitHub](https://github.com/dokrak/Leader_data_service/issues)
4. Contact your system administrator

---

**Happy File Managing! 📁✨**
