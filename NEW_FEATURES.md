# 🏥 Leader Data Service - New Features Documentation

## ✨ New Features Added

### 1. 👥 User Authentication System
- **Login Page**: `/login`
- **Register Page**: `/register`
- Secure authentication using Laravel Sanctum
- Session management with API tokens

### 2. 🏢 Hospital Team/Department Management
**10 Pre-configured Teams:**
1. **Emergency Department** (ER)
2. **Cardiology** (CARDIO)  
3. **Pediatrics** (PEDS)
4. **Surgery** (SURGERY)
5. **Radiology** (RAD)
6. **Laboratory** (LAB)
7. **Pharmacy** (PHARMA)
8. **Nursing** (NURSING)
9. **Administration** (ADMIN)
10. **IT Department** (IT)

### 3. 👨‍💼 User Roles & Classification
**Three User Roles:**
- **Admin**: Full system access, can manage all teams
- **Team Leader**: Can manage their team's files and members
- **User**: Can upload and manage their own files

### 4. 📁 Team-Based File Management
- Files are now associated with teams
- Filter files by team
- Team-specific dashboards
- Track which user uploaded each file

---

## 🔐 Default Test Accounts

### Administrator Account
```
Email: admin@hospital.local
Password: admin123
Role: Admin
Team: Administration
```

### Team Leader Accounts
```
ER Team Leader:
Email: er.leader@hospital.local
Password: password123
Role: Team Leader
Team: Emergency Department

Cardiology Team Leader:
Email: cardio.leader@hospital.local
Password: password123
Role: Team Leader
Team: Cardiology
```

---

## 📡 New API Endpoints

### Authentication Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/api/register` | Register new user |
| POST | `/api/login` | Login user |
| POST | `/api/logout` | Logout user (requires auth) |
| GET | `/api/me` | Get current user (requires auth) |

### Team Endpoints
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/api/teams` | List all teams |
| GET | `/api/teams/{id}` | Get team details (requires auth) |
| GET | `/api/teams/{id}/files` | Get team files (requires auth) |
| GET | `/api/teams/{id}/dashboard` | Get team dashboard stats (requires auth) |

### Updated File Endpoints
**NOTE:** Most file operations now require authentication

| Method | Endpoint | Description | Auth Required |
|--------|----------|-------------|---------------|
| GET | `/api/files` | List all files | No |
| POST | `/api/files` | Upload file | Yes |
| GET | `/api/files/{id}` | Get file details | Yes |
| GET | `/api/files/{id}/download` | Download file | Yes |
| PUT | `/api/files/{id}` | Update file | Yes |
| DELETE | `/api/files/{id}` | Delete file | Yes |
| GET | `/api/storage/stats` | Storage statistics | No |

---

## 🔧 Database Changes

### New Tables
1. **teams** - Hospital departments/teams
2. **personal_access_tokens** - API authentication tokens

### Updated Tables
1. **users** - Added `role`, `team_id`, `is_active`
2. **uploaded_files** - Added `team_id`, `user_id`

---

## 📋 How to Use

### For Hospital Staff

#### 1. Register an Account
1. Go to `/register`
2. Enter your details
3. Select your team/department
4. Create password
5. Submit

####  2. Login
1. Go to `/login`
2. Enter email and password
3. Click Login
4. You'll be redirected to the file manager

#### 3. Upload Files
1. After logging in, go to file manager
2. Select your file
3. **Select your team** from dropdown
4. Add description (optional)
5. Choose category
6. Upload

#### 4. View Team Files
- Files are automatically filtered by team
- Use search and category filters
- Download or delete files as needed

### For Administrators

#### Create New Teams
```bash
php artisan tinker
```
```php
App\Models\Team::create([
    'name' => 'Oncology',
    'code' => 'ONCO',
    'description' => 'Cancer treatment department',
    'is_active' => true
]);
```

#### Create Users
```bash
php artisan tinker
```
```php
App\Models\User::create([
    'name' => 'Dr. Example',
    'email' => 'doctor@hospital.local',
    'password' => Hash::make('password123'),
    'role' => 'user',  // or 'admin', 'team_leader'
    'team_id' => 1,
    'is_active' => true
]);
```

---

## 🧪 Testing

### Test Login API
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@hospital.local","password":"admin123"}'
```

### Test Registration API
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@hospital.local",
    "password": "password123",
    "password_confirmation": "password123",
    "team_id": 1
  }'
```

### Test File Upload with Authentication
```bash
TOKEN="your-auth-token-here"

curl -X POST http://localhost:8000/api/files \
  -H "Authorization: Bearer $TOKEN" \
  -F "file=@document.pdf" \
  -F "team_id=1" \
  -F "uploaded_by=Test User" \
  -F "category=documents"
```

---

## 🔒 Security Features

1. **Password Hashing**: All passwords are hashed using bcrypt
2. **API Token Authentication**: Secure token-based authentication
3. **CSRF Protection**: Built-in Laravel CSRF protection
4. **Input Validation**: All inputs are validated
5. **SQL Injection Protection**: Using Eloquent ORM
6. **Access Control**: Role-based access control

---

## 📊 Team Dashboard Features

Each team gets:
- Total files count
- Total storage used
- Number of team members
- Files uploaded this month
- Category breakdown
- Recent uploads list

---

## 🚀 Next Steps for Production

### 1. Environment Configuration
Update `.env` file:
```env
APP_ENV=production
APP_DEBUG=false
SANCTUM_STATEFUL_DOMAINS=your-hospital-domain.com
SESSION_DOMAIN=.your-hospital-domain.com
```

### 2. Email Verification (Optional)
Enable email verification for new users:
- Configure mail settings in `.env`
- Implement email verification routes

### 3. Password Reset (Optional)
Add password reset functionality:
```bash
php artisan make:controller Auth/PasswordResetController
```

### 4. Two-Factor Authentication (Advanced)
Consider adding 2FA for enhanced security

---

## 📱 Access URLs

- **Login**: `http://localhost:8000/login`
- **Register**: `http://localhost:8000/register`
- **File Manager**: `http://localhost:8000/`
- **API Documentation**: See API Endpoints section above

**For Codespaces:**
Replace `localhost:8000` with your Codespace URL:
`https://sturdy-space-robot-xrj9wxgp6v7265gg-8000.app.github.dev`

---

## 🐛 Troubleshooting

### Cannot Login
- Check database has been migrated
- Verify seeder has run
- Check credentials match test accounts

### File Upload Fails
- Ensure you're logged in
- Check team_id is provided
- Verify storage permissions

### API Returns 401 Unauthorized
- Check token is included in Authorization header
- Format: `Authorization: Bearer YOUR_TOKEN_HERE`
- Token expires - login again to get new token

---

## 📈 Statistics

**What's Been Added:**
- ✅ 3 new database tables
- ✅ 10 hospital teams/departments  
- ✅ 3 user roles (Admin, Team Leader, User)
- ✅ 12 new API endpoints
- ✅ 2 new web pages (Login, Register)
- ✅ Team-based file filtering
- ✅ User authentication system
- ✅ API token security
- ✅ 3 default test accounts

---

**🎉 Your Leader Data Service now has a complete authentication and team management system!**
