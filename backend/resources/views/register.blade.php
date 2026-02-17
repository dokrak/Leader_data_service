<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Leader Data Service</title>
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

        .register-container {
            background: white;
            border-radius: 16px;
            padding: 40px;
           width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
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

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2d3748;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus,
        .form-group select:focus {
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

        .alert.success {
            background: #c6f6d5;
            color: #22543d;
            border-left: 4px solid #48bb78;
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
    <div class="register-container">
        <div class="logo">
            <h1>🏥</h1>
            <h1>Create Account</h1>
            <p>Join Leader Data Service</p>
        </div>

        <div id="alertBox" class="alert"></div>

        <form id="registerForm">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" required placeholder="Dr. John Doe">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="john.doe@hospital.local">
            </div>

            <div class="form-group">
                <label for="team_id">Your Team/Department</label>
                <select id="team_id" name="team_id" required>
                    <option value="">Select your team...</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required minlength="8" placeholder="At least 8 characters">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Re-enter password">
            </div>

            <button type="submit" class="btn" id="registerBtn">Create Account</button>
        </form>

        <div class="link-group">
            <p>Already have an account? <a href="login">Login here</a></p>
        </div>
    </div>

    <script>
        const API_BASE = '/api';

        // Load teams
        async function loadTeams() {
            try {
                const response = await fetch(`${API_BASE}/teams`);
                const teams = await response.json();

                const select = document.getElementById('team_id');
                teams.forEach(team => {
                    const option = document.createElement('option');
                    option.value = team.id;
                    option.textContent = team.name;
                    select.appendChild(option);
                });
            } catch (error) {
                console.error('Error loading teams:', error);
            }
        }

        loadTeams();

        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                team_id: document.getElementById('team_id').value,
                password: document.getElementById('password').value,
                password_confirmation: document.getElementById('password_confirmation').value,
            };

            if (formData.password !== formData.password_confirmation) {
                showAlert('Passwords do not match', 'error');
                return;
            }

            const registerBtn = document.getElementById('registerBtn');
            registerBtn.disabled = true;
            registerBtn.textContent = 'Creating account...';

            try {
                const response = await fetch(`${API_BASE}/register`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (response.ok) {
                    showAlert('Account created successfully! Redirecting to login...', 'success');
                    
                    // Store token and redirect
                    localStorage.setItem('auth_token', data.token);
                    localStorage.setItem('user', JSON.stringify(data.user));
                    
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                } else {
                    const errorMsg = data.errors
                        ? Object.values(data.errors).flat().join(', ')
                        : data.message || 'Registration failed';
                    showAlert(errorMsg, 'error');
                }
            } catch (error) {
                showAlert('Error connecting to server: ' + error.message, 'error');
            } finally {
                registerBtn.disabled = false;
                registerBtn.textContent = 'Create Account';
            }
        });

        function showAlert(message, type) {
            const alertBox = document.getElementById('alertBox');
            alertBox.textContent = message;
            alertBox.className = `alert ${type} show`;
        }
    </script>
</body>
</html>
