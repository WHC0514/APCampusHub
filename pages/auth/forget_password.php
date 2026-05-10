<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APCampusHub</title>

    <!-- CSS -->
    <link rel="stylesheet" href="../../assets/css/auth/login.css">
</head>
<body>
    
    <div class="overlay">

        <!-- LOGO -->
        <div class="top-section">

            <img src="../../assets/images/logo.png" alt="Logo" class="logo">

            <h1>APCampusHub</h1>

            <p class="slogan">An All-in-one Campus Management Application</p>

        </div>

        <!-- Verification Box -->
        <div class="form-container">

            <form action="forget_password_process.php" method="POST">

                <h2>Forget Password</h2>

                <div class="input-group">

                    <input type="text" name="username" placeholder="TP Number" required>

                </div>

                <div class="input-group">

                    <input type="text" name="ic_number" placeholder="Identity Card Number" required>

                </div>

                <!-- Go Back to Login Page Function -->
                <div class="forget-password">

                    <a href="login.php">

                        Back to Login

                    </a>

                </div>

                <!-- Submit Button -->
                <button type="submit" name="loginBtn">

                    Verify

                </button>

            </form>
        </div>
    </div>

</body>
</html>