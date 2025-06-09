<?php
session_start();
include 'db_connect.php';
include 'functions.php';

$page = isset($_GET['page']) ? $_GET['page'] : 'coord_content';


if (!isset($_SESSION['department'])) {
    die("Unauthorized access!");
}

$department = $_SESSION['department'];


$sql = "SELECT * FROM intellectual_properties WHERE department = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="ITSO.png">  

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
<style>
.no-file {
    color: #999;
    font-style: italic;
}

.status-badge {
  display: inline-block;
  padding: 4px 10px;
  font-size: 12px;
  font-weight: bold;
  color: white;
  border-radius: 12px;
  text-align: center;
}

.status-completed { background-color: #28a745; } 
.status-pending { background-color: #dc3545; }   
.status-ongoing { background-color: #ffc107; color: #000; }

.update-badge {
    padding: 6px 16px;
    background-color:rgb(99, 163, 231);
    color: white;
    border: none;
    border-radius: 50px;
    font-size: 0.8rem;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    display: inline-flex;
    align-items: center;
    text-transform: uppercase;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.update-badge:hover {
    background-color: #0056b3;
}

.modal {
    display: none;
    position: fixed; 
    z-index: 1000; 
    padding-top: 100px; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    overflow: auto; 
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px 30px;
    border: 1px solid #888;
    width: 50%;
    border-radius: 10px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

.submit-btn {
    background-color: #4CAF50;
    color: white;
    padding: 10px 25px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.submit-btn:hover {
    background-color: #45a049;
}
</style>
</head>
<body>

<div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <img src="assets/imgs/itsolog1.png" alt="logos" style="max-width: 285px; max-height: 235px;">
                    </a>
                </li>

                <li>
                    <a href="index.php?page=coord_content">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="index.php?page=create_application">
                        <span class="icon">
                            <ion-icon name="create-outline"></ion-icon>
                        </span>
                        <span class="title">Create Application</span>
                    </a>
                </li>

                <li>
                    <a href="index.php?page=app_form">
                        <span class="icon">
                            <ion-icon name="apps-outline"></ion-icon>
                        </span>
                        <span class="title">Application Form</span>
                    </a>
                </li>
            
                <li>
                    <a href="login.php">
                        <span class="icon">
                            <ion-icon name="log-out-outline"></ion-icon>
                        </span>
                        <span class="title">Sign Out</span>
                    </a>
                </li>
            </ul>
        </div>
<br>
        <div class="main">
            <div id="content">
                <?php
                if ($page == "coord_content") {
                    include 'coord_content.php';
                } elseif ($page == "create_application") {
                    include 'create_application.php';
                } elseif ($page == "app_form") {
                    include 'app_form.html';
                } else {
                    echo "<p>Page not found.</p>";
                }
                ?>

    </div>
</body>
</html>
