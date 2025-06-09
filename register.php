<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/signup.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="icon" type="image/png" href="ITSO.png">
</head>
<body>
    <div class="wrapper">   
        <span class="bg-animate2"></span>
        
        <div class="form-box register">
            <h2>Sign Up</h2>
            <form action="process_register.php" method="POST">
                <div class="input-box">
                    <input type="text" name="username" autocomplete="off" required>
                    <label>Username</label>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" name="password" required>
                    <label>Password</label>
                    <i class='bx bxs-lock-alt' ></i>
                </div>

                <div class="input-box">
                <p>Role</p>
                    <select name="role" class="form-control" required>
                   <!-- <option value="Admin">Admin</option> -->
                        <option value="Coordinator">Coordinator</option>
                    </select>
                </div>
                
                <div class="input-box">
                <p>Department</p>
                        <select name="department" class="form-control" required>
                            <option value=" "> </option>
                            <option value="CCS">College of Computer Studies</option>
                            <option value="CTE">College of Teacher Education</option>
                            <option value="CFND">College of Food Nutrition and Dietetics</option>
                            <option value="CIT">College of Industrial Technology</option>
                            <option value="COA">College of Agriculture</option>
                            <option value="CAS">College of Arts nad Science</option>
                            <option value="CBAA">College of Business Administration and Accountancy</option>
                            <option value="COE">College of Engineering</option>
                            <option value="CCJE">College of Criminal Justice Education</option>
                            <option value="COF">College of Fisheries</option>
                            <option value="CHMT">College of Hospitality Management and Tourism</option>
                            <option value="CNAH">College of Nursing and Allied Health</option>
                        </select>
                </div>

                <button type="submit" class="btn">Sign Up</button>
                <p class="error-message"> <?php if(isset($_GET['error'])) echo $_GET['error']; ?> </p>
                <div class="logreg-link">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>

        <div class="info-text register">
            <h2>Create an Account</h2>
            <img src="assets/imgs/itsolog1.png">
            <p></p>
        </div>
</body>
</html>
