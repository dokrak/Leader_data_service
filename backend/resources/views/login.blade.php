<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Leader Data Service</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .logo-image {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
        }

        .logo h1 {
            color: #667eea;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .logo p {
            color: #718096;
            font-size: 1rem;
        }

        .hospital-identifier {
            font-size: 0.85rem;
            color: #a0aec0;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 16px;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        .alert.error {
            background: #fed7d7;
            color: #742a2a;
            border-left: 4px solid #f56565;
        }

        .alert.show {
            display: block;
        }

        .link-group {
            text-align: center;
            margin-top: 25px;
            color: #718096;
        }

        .link-group a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .link-group a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="/images/chomthong-hospital-logo.svg" alt="Chomthong Hospital Logo" class="logo-image">
            <h1>Leader Data Service</h1>
            <p>Hospital File Management System</p>
            <p class="hospital-identifier">Chomthong Hospital</p>
        </div>

        <div id="alertBox" class="alert"></div>

        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="your.email@hospital.local">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>

            <button type="submit" class="btn" id="loginBtn">Login</button>
        </form>

        <div class="link-group">
            <p>Don't have an account? <a href="register">Register here</a></p>
        </div>

        <div class="link-group" style="margin-top: 15px; font-size: 0.9rem;">
            <p><strong>Demo Accounts:</strong></p>
            <p>Admin: admin@hospital.local / admin123</p>
            <p>ER Leader: er.leader@hospital.local / password123</p>
        </div>
    </div>

    <script>
        const API_BASE = '/api';

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const loginBtn = document.getElementById('loginBtn');

            loginBtn.disabled = true;
            loginBtn.textContent = 'Logging in...';

            try {
                const response = await fetch(`${API_BASE}/login`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email, password })
                });

                const data = await response.json();

                if (response.ok) {
                    // Store token and user data
                    localStorage.setItem('auth_token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    // Redirect to file manager
                    window.location.href = '/';
                } else {
                    showAlert(data.message || 'Login failed', 'error');
                }
            } catch (error) {
                showAlert('Error connecting to server: ' + error.message, 'error');
            } finally {
                loginBtn.disabled = false;
                loginBtn.textContent = 'Login';
            }
        });

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = `alert ${type} show`;
            setTimeout(() => {
                alertBox.classList.remove('show');
            }, 5000);
        }
    </script>
</body>
</html>
