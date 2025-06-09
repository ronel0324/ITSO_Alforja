<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login & Signup</title>
    <link rel="stylesheet" href="assets/css/login_signup.css">
    <link rel="icon" type="image/png" href="ITSO.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="wrapper">
        <span class="bg-animate"></span>

        <div class="form-box login">
            <h2>Login</h2>
            <form action="process_login.php" id="loginForm" method="POST">
                <div class="input-box">
                    <input type="text" name="username" autocomplete="off" required>
                    <label for="username">Username</label>
                </div>
                <div class="input-box">
                    <input type="password" id="password" name="password" required>
                    <label for="password">Password</label>
                    <i class='bx bx-show toggle-password' onclick="togglePassword()"></i>
                </div>
                <button type="submit" class="btn animation">Login</button>
                <div class="logreg-link animation" style="--i:4;">
                    <p>Don't have an account? <a href="register.php">Sign Up</a></p>
                </div>
            </form>
        </div>
        <div class="info-text login">
            <h2>Welcome</h2>
            <img src="assets/imgs/itsolog1.png">
        </div>
    </div>

    
<script>
    fetch("process_login.php", {
        method: "POST",
        body: formData
    }).then(response => response.text())
    .then(data => {
        if (data.includes("success")) {
            window.location.href = "index.php";
        } else {
            alert("Login failed: " + data);
        }
        });
        
        function togglePassword() {
            const password = document.getElementById("password");
            const toggleIcon = document.querySelector(".toggle-password");

            if (password.type === "password") {
                password.type = "text";
                toggleIcon.classList.remove("bx-show");
                toggleIcon.classList.add("bx-hide");
            } else {
                password.type = "password";
                toggleIcon.classList.remove("bx-hide");
                toggleIcon.classList.add("bx-show");
            }
        }

    </script>
</body>
</html>