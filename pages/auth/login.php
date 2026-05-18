<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <link rel="stylesheet" href="../../assets/css/auth/login.css">
</head>
<body>
    
    <div class="overlay">

        <!-- Logo -->
        <div class="top-section">

            <img src="../../assets/images/logo.png" alt="Logo" class="logo">

            <h1>APCampusHub</h1>

            <p class="slogan">An All-in-one Campus Management Application</p>

        </div>

        <!-- Login Box -->
        <div class="form-container">

            <form action="login_process.php" method="POST">

                <div class="input-group">

                    <input type="text" name="username" placeholder="TP Number" required>

                </div>

                <div class="input-group">

                    <input type="password" name="password" placeholder="Password" required>

                </div>

                <!-- Forget Password Function -->
                <div class="forget-password">

                    <a href="forget_password.php">

                        Forget Password?

                    </a>

                </div>

                <!-- Submit Button -->
                <button type="submit" name="loginBtn">

                    Login

                </button>

            </form>
        </div>
    </div>

</body>
</html>