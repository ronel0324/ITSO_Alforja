<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'db_connect.php';

if (!isset($_SESSION['username']) || $_SESSION['role'] != "Admin") {
    header("Location: login.php");
    exit();
}

$monthFilter = isset($_GET['month']) && $_GET['month'] !== '' ? $_GET['month'] : null;

if ($monthFilter) {
    $sql = "SELECT * FROM intellectual_properties 
            WHERE MONTH(date_submitted_to_itso) = '$monthFilter'
            AND date_submitted_to_itso >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            ORDER BY department, classification, date_submitted_to_itso DESC";
} else {
    $sql = "SELECT * FROM intellectual_properties 
            WHERE date_submitted_to_itso >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
            ORDER BY department, classification, date_submitted_to_itso DESC";
}
$result = $conn->query($sql);

$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $dept = $row['department'];
    $class = $row['classification'];
    $groupedData[$dept][$class][] = $row;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports - IP Monitoring</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .ip-table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        .ip-table th, .ip-table td { padding: 10px; border: 1px solid #ccc; }
        .ip-table th { background-color: #f2f2f2; }
        .status-completed { background-color: #28a745; color: white; padding: 5px 10px; border-radius: 8px; }
        .status-pending { background-color: #dc3545; color: white; padding: 5px 10px; border-radius: 8px; }
        .status-ongoing { background-color: #ffc107; color: black; padding: 5px 10px; border-radius: 8px; }
        .export-btn { background-color: #007bff; color: white; padding: 10px 20px; margin: 10px 0; text-decoration: none; border-radius: 5px; display: inline-block; }
        .export-btn:hover { background-color: #0056b3; }
        h3 { margin-top: 40px; font-size: 32px; }
        h4 { font-size: 20px; }
        form label, form select {
    font-size: 16px;
    margin-right: 10px;
}

.filterpos {
    text-align: right;
    bottom: 1000px;
}
    </style>
</head>
<body>

    <h2>Intellectual Properties Report</h2>
    <a href="export_report.php" class="export-btn" target="_blank">📄 Export as PDF</a>

    <div class="filterpos">
    <form method="GET" style="margin-bottom: 20px;">
    <label for="month">Filter by Month:</label>
    <input type="hidden" name="page" value="reports">
    <select name="month" id="month" onchange="this.form.submit()">
        <option value="">-- All Months --</option>
        <?php
        for ($m = 1; $m <= 12; $m++) {
            $monthValue = str_pad($m, 2, '0', STR_PAD_LEFT);
            $monthName = date('F', mktime(0, 0, 0, $m, 10));
            $selected = (isset($_GET['month']) && $_GET['month'] === $monthValue) ? 'selected' : '';
            echo "<option value='$monthValue' $selected>$monthName</option>";
        }
        ?>
    </select>
</form>
    </div>

    <?php foreach ($groupedData as $dept => $classifications): ?>
        <h3>Department: <?= htmlspecialchars($dept) ?></h3>

        <?php foreach ($classifications as $class => $records): ?>
            <h4> <?= htmlspecialchars($class) ?></h4>
            
            <table class="ip-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Department</th>
                        <th>Authors</th>
                        <th>Status</th>
                        <th>Filing Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($records as $row): ?>
                        <tr>
                            <td><?= $row['ip_id'] ?></td>
                            <td><?= htmlspecialchars($row['title']) ?></td>
                            <td><?= htmlspecialchars($row['classification']) ?></td>
                            <td><?= htmlspecialchars($row['department']) ?></td>
                            <td><?= htmlspecialchars($row['authors']) ?></td>
                            <td>
                                <?php
                                $status = $row['status'];
                                $classStatus = $status == 'Completed' ? 'status-completed' :
                                               ($status == 'Pending' ? 'status-pending' : 'status-ongoing');
                                echo "<span class='$classStatus'>$status</span>";
                                ?>
                            </td>
                            <td><?= date("F j, Y", strtotime($row['date_submitted_to_itso'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    <?php endforeach; ?>

</body>
</html>
