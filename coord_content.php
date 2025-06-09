<?php
include 'db_connect.php';

$department = $_SESSION['department'] ?? 'All Departments';

$query = "SELECT * FROM intellectual_properties WHERE department = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $department);
$stmt->execute();
$result = $stmt->get_result();

function filenameOrNoFile($file) {
    if (!empty($file) && $file !== 'X' && $file !== '0') {
        return htmlspecialchars($file);
    } else {
        return "<span class='no-file'>No File</span>";
    }
}
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coordinator Dashboard</title>
    <link rel="icon" type="image/png" href="ITSO.png">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow-y: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            z-index: 10000;
            position: relative;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        .table-container {
    overflow-x: auto;
    width: 100%;
}

    </style>
</head>


<div style="overflow-x: auto; width: 100%;">
    <h2>All Intellectual Properties (Department: <?php echo htmlspecialchars($department); ?>)</h2>
    <div style="overflow-x: auto; width: 100%; height: 900px; border-bottom: 1px solid #ccc; position: relative; z-index: 10;">
    <table class="table table-bordered mt-3" style="min-width: 1200px; width: 100%; height: 50px; border-collapse: collapse;">
            <thead>
                <tr>
                    <th>IP#</th>
                    <th>Title</th>
                    <th>Authors</th>
                    <th>Applicant Name</th>
                    <th>Classification</th>
                    <th>Endorsement Letter</th>
                    <th>Status</th>
                    <th>Application Form</th>
                    <th>Submitted to IPOPHIL</th>
                    <th>Application Fee</th>
                    <th>Issued Certificate</th>
                    <th>Project File</th>
                    <th>Date Submitted to ITSO</th>
                    <th>Date Submitted to IPOPHIL</th>
                    <th>Expiration Date</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $status_class = '';
                        switch (strtolower($row['status'])) {
                            case 'completed': $status_class = 'status-completed'; break;
                            case 'pending': $status_class = 'status-pending'; break;
                            case 'ongoing': $status_class = 'status-ongoing'; break;
                        }                
                    ?>
                    <tr>
                        <td><?php echo $row['ip_id']; ?></td>
                        <td><?php echo htmlspecialchars($row['title']); ?></td>
                        <td><?php echo htmlspecialchars($row['authors']); ?></td>
                        <td><?php echo htmlspecialchars($row['applicant_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['classification']); ?></td>
                        <td><?php echo filenameOrNoFile($row['endorsement_letter']); ?></td>
                        <td><span class='status-badge <?php echo $status_class; ?>'><?php echo htmlspecialchars($row["status"]); ?></span></td>
                        <td><?php echo filenameOrNoFile($row['application_form']); ?></td>
                        <td><?php echo htmlspecialchars($row['submitted'] ? 'Yes' : 'No'); ?></td>
                        <td><?php echo filenameOrNoFile($row['application_fee']); ?></td>
                        <td><?php echo filenameOrNoFile($row['issued_certificate']); ?></td>
                        <td><?php echo filenameOrNoFile($row['project_file']); ?></td>
                        <td><?php echo !empty($row['date_submitted_to_itso']) ? date('F d, Y', strtotime($row['date_submitted_to_itso'])) : 'No Date'; ?></td>
                        <td>
                            <?php 
                            echo !empty($row['date_submitted_to_ipophil']) && $row['date_submitted_to_ipophil'] !== "0000-00-00" 
                                ? date('F d, Y', strtotime($row['date_submitted_to_ipophil'])) 
                                : 'N/A'; 
                            ?>
                        </td>
                        <td>
                            <?php 
                                echo (!empty($row['expiration_date']) && $row['expiration_date'] !== '0000-00-00') 
                                    ? date('F d, Y', strtotime($row['expiration_date'])) 
                                    : 'N/A'; 
                            ?>
                        </td>
                        <td>
                            <button type="button" onclick="openUpdateModal(<?php echo $row['ip_id']; ?>, '<?php echo $row['classification']; ?>')" class="update-badge">Update</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
    </table>
</div>

<div id="updateModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Update Files</h2>
        <form id="updateForm" action="process_update.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" id="ip_id" name="ip_id">
            <input type="hidden" id="classification" name="classification">

            <div class="form-group">
                <label>Endorsement Letter:</label>
                <input type="file" name="endorsement_letter">
            </div>

            <div class="form-group">
                <label>Application Form:</label>
                <input type="file" name="application_form">
            </div>

            <div class="form-group">
                <label>Application Fee:</label>
                <input type="file" name="application_fee">
            </div>

            <div class="form-group">
                <label>Issued Certificate:</label>
                <input type="file" name="issued_certificate">
            </div>

            <div class="form-group">
                <label>Project File:</label>
                <input type="file" name="project_file">
            </div>

            <div class="form-group">
                <label>Date Submitted to IPOPHIL:</label>
                <input type="date" name="date_submitted_to_ipophil">
            </div>

            <button type="submit" class="submit-btn">Update Files</button>
        </form>
    </div>
</div>

<script>
    function openUpdateModal(ip_id, classification) {
        document.getElementById('ip_id').value = ip_id;
        document.getElementById('classification').value = classification;
        document.getElementById('updateModal').style.display = "block";
    }

    function closeModal() {
        document.getElementById('updateModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('updateModal')) {
            closeModal();
        }
    };

        document.querySelector("form").addEventListener("submit", function (e) {
        const maxSize = 100 * 1024 * 1024; // 40MB
        let totalSize = 0;

        const fileInputs = this.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            if (input.files.length > 0) {
                totalSize += input.files[0].size;
            }
        });

        if (totalSize > maxSize) {
            alert("Total file size exceeds 100MB. Please upload smaller files.");
            e.preventDefault();
        }
    });

    
</script>

</body>
</html>