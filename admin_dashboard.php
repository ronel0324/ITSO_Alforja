<?php
session_start();
include 'db_connect.php';

// check kung admin
if (!isset($_SESSION['username']) || $_SESSION['role'] != "Admin") {
    header("Location: login.php");
    exit();
}

// count completed
$sql_approved = "SELECT COUNT(*) as approved_count FROM intellectual_properties WHERE status = 'Completed'";
$result_approved = $conn->query($sql_approved);
$approved_count = 0;

if ($result_approved->num_rows > 0) {
    $row_approved = $result_approved->fetch_assoc();
    $approved_count = $row_approved['approved_count'];
}

// count pending
$sql_rejected = "SELECT COUNT(*) as rejected_count FROM intellectual_properties WHERE status = 'Pending'";
$result_rejected = $conn->query($sql_rejected);
$rejected_count = 0;
if ($result_rejected->num_rows > 0) {
    $row_rejected = $result_rejected->fetch_assoc();
    $rejected_count = $row_rejected['rejected_count'];
}

// count ongoing 
$sql_ongoing = "SELECT COUNT(*) as ongoing_count FROM intellectual_properties WHERE status = 'Ongoing'";
$result_ongoing = $conn->query($sql_ongoing);
$ongoing_count = 0;
if ($result_ongoing->num_rows > 0) {
    $row_ongoing = $result_ongoing->fetch_assoc();
    $ongoing_count = $row_ongoing['ongoing_count'];
}

// all records
$sql_all = "SELECT COUNT(*) as all_count FROM intellectual_properties";
$result_all = $conn->query($sql_all);
$all_count = 0;
if ($result_all->num_rows > 0) {
    $row_all = $result_all->fetch_assoc();
    $all_count = $row_all['all_count'];
}

$sql_authors = "SELECT authors FROM intellectual_properties";
$result_authors = $conn->query($sql_authors);
$authors = array();

if ($result_authors->num_rows > 0) {
    while ($row_authors = $result_authors->fetch_assoc()) {
        $authors[] = $row_authors['authors'];
    }
}

$edit_ip_id = isset($_GET['ip_id']) ? $_GET['ip_id'] : null;

function generateFileLink($filename) {
    if ($filename && file_exists("uploads/" . $filename)) {
        $filePath = "uploads/$filename";
        return "
            <a href='$filePath' target='_blank' title='View'>
                <ion-icon name='eye-outline' style='font-size: 20px; margin-right: 5px;'></ion-icon>
            </a>
            <a href='$filePath' download title='Download'>
                <ion-icon name='download-outline' style='font-size: 20px;'></ion-icon>
            </a>
        ";
    } else {
        return "<span style='color: gray;'>No file</span>";
    }
}

 
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard_content';

$search = isset($_GET['search']) ? $_GET['search'] : '';

$sql = "SELECT * FROM intellectual_properties";

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

$sql = "SELECT * FROM intellectual_properties";

if (!empty($statusFilter)) {
    $sql .= " WHERE status = '$statusFilter'";
}

$result = $conn->query($sql);



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="ITSO.png">  
    <style>
.custom-modal {
  display: none;
  position: fixed;
  z-index: 999;
  left: 0; top: 0;
  width: 100%; height: 100%;
  background-color: rgba(0, 0, 0, 0.6);
  justify-content: center;
  align-items: center;
}

.modal-content-box {
  background-color: white;
  padding: 20px;
  width: 90%;
  max-width: 500px;
  border-radius: 10px;
  position: relative;
  animation: fadeIn 0.3s ease;
}

.close-modal {
  position: absolute;
  right: 10px;
  top: 5px;
  font-size: 24px;
  cursor: pointer;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-20px); }
  to { opacity: 1; transform: translateY(0); }
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

.status-completed { background-color: #28a745; }  /* Green */
.status-pending { background-color: #dc3545; }    /* Red */
.status-ongoing { background-color: #ffc107; color: #000; } /* Yellow with black text */

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

.generate-badge {
    padding: 6px 16px;
    background-color: #28a745;
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

.generate-badge:hover {
    background-color: #1e7e34;
}

.delete-badge {
    background-color: transparent;
    border: none;
    color: red;
    cursor: pointer;
    font-weight: bold;
}

.delete-badge:hover {
    background-color: white;
}

</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

</head>
<body>
    
    <div class="container">
        <div class="navigation">
            <ul>
                <li>
                    <a href="#">
                        <span class="icon">
                        <img src="assets/imgs/itsolog1.png" alt="logos" style="max-width: 285px; max-height: 235px;">
                        </span>
                    </a>
                </li>
                <br><br><br><br><br>
                <li>
                    <a href="admin_dashboard.php?page=dashboard_content">
                        <span class="icon">
                            <ion-icon name="home-outline"></ion-icon>
                        </span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <li>
                    <a href="admin_dashboard.php?page=create_application">
                        <span class="icon">
                            <ion-icon name="create-outline"></ion-icon>
                        </span>
                        <span class="title">Create Application</span>
                    </a>
                </li>

                <li>
                    <a href="admin_dashboard.php?page=app_form">
                        <span class="icon">
                            <ion-icon name="apps-outline"></ion-icon>
                        </span>
                        <span class="title">Application Forms</span>
                    </a>
                </li>

                <li>
                    <a href="admin_dashboard.php?page=reports">
                        <span class="icon">
                            <ion-icon name="document-text-outline"></ion-icon>
                        </span>
                        <span class="title">Reports</span>
                    </a>
                </li>


                <li>
                    <a href="admin_dashboard.php?page=acc_manager">
                        <span class="icon">
                            <ion-icon name="people-outline"></ion-icon>
                        </span>
                        <span class="title">Account Manager</span>
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

        <div class="main">
            <div id="content">
                <?php
                if ($page == "dashboard_content") {
                    include 'dashboard_content.php';
                } elseif ($page == "create_application") {
                    include 'create_application1.php';
                } elseif ($page == "app_form") {
                    include 'app_form.html';
                } elseif ($page == "acc_manager") {
                    include 'manage_account.php';
                } elseif ($page == "reports") {
                    include 'reports.php';
                } else {
                    echo "<p>Page not found.</p>";
                }
                ?>

    </div>
            </div>

            <script>
document.querySelectorAll('.open-modal').forEach(btn => {
  btn.addEventListener('click', function () {
    const modalId = this.getAttribute('data-modal');
    document.getElementById(modalId).style.display = 'flex';
  });
});

document.querySelectorAll('.close-modal').forEach(btn => {
  btn.addEventListener('click', function () {
    const modalId = this.getAttribute('data-modal');
    document.getElementById(modalId).style.display = 'none';
  });
});

window.addEventListener('click', function (e) {
  document.querySelectorAll('.custom-modal').forEach(modal => {
    if (e.target === modal) {
      modal.style.display = 'none';
    }
  });
});

const topScroll = document.getElementById('scroll-wrapper-top');
    const bottomScroll = document.getElementById('scroll-wrapper-bottom');

    bottomScroll.addEventListener('scroll', () => {
        topScroll.scrollLeft = bottomScroll.scrollLeft;
    });

    topScroll.addEventListener('scroll', () => {
        bottomScroll.scrollLeft = topScroll.scrollLeft;
    });

    window.addEventListener('load', () => {
        topScroll.scrollLeft = bottomScroll.scrollLeft;
        topScroll.firstChild?.remove();
        const scroller = document.createElement('div');
        scroller.style.width = bottomScroll.scrollWidth + "px";
        scroller.style.height = '1px';
        topScroll.appendChild(scroller);
    });
</script>

</body>
</html>