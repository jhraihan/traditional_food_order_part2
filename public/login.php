<?php
session_start();
if (isset($_SESSION['user'])) {
    header("Location: admin.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Bangladeshi Food</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(to right, #006a4e, #3d9970);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 400px;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
            color: #006a4e;
        }

        .logo i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            width: 100%;
            padding: 12px;
            background: #006a4e;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 1rem;
        }

        button:hover {
            background: #00563f;
        }

        .portal-links {
            text-align: center;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        .portal-links a {
            display: block;
            margin: 0.5rem 0;
            color: #006a4e;
            text-decoration: none;
        }

        .error {
            color: red;
            text-align: center;
            margin-top: 1rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="logo">
            <i class="fas fa-utensils"></i>
            <h2>Bangladeshi Food</h2>
            <p>Access Portal</p>
        </div>

        <form id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="role">Login As</label>
                <select id="role" name="role" required>
                    <option value="">Select Role</option>
                    <option value="admin">Admin</option>
                    <option value="restaurant">Restaurant</option>
                </select>
            </div>

            <button type="submit">Login</button>

            <div id="errorMessage" class="error"></div>
        </form>

        <div class="portal-links">
            <a href="index.html">← Customer Site</a>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('role').value;

            // Simple client-side validation
            const validUsers = {
                'admin': {
                    password: 'admin123',
                    redirect: 'admin.html'
                },
                'restaurant': {
                    password: 'restaurant123',
                    redirect: 'index2.html'
                }
            };

            if (validUsers[username] && validUsers[username].password === password && role === username) {
                // Store login state in localStorage
                localStorage.setItem('loggedIn', 'true');
                localStorage.setItem('userRole', role);
                localStorage.setItem('username', username);

                window.location.href = validUsers[username].redirect;
            } else {
                document.getElementById('errorMessage').textContent = 'Invalid username, password or role selection!';
            }
        });
    </script>
</body>

</html>