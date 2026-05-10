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

        <!-- Reset Password Box -->
        <div class="form-container">

            <form action="reset_password_process.php" method="POST">

                <h2>Reset Password</h2>

                <div class="input-group">

                    <input type="text" name="password" placeholder="Password" required>

                </div>

                <div class="input-group">

                    <input type="text" name="confirm_password" placeholder="Confirm Password" required>

                </div>

                <!-- Submit Button -->
                <button type="submit" name="loginBtn">

                    Set Password

                </button>

            </form>
        </div>
    </div>

</body>
</html>