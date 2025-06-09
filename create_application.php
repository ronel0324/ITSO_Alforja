<?php
include 'db_connect.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Intellectual Property</title>
    <link rel="stylesheet" href="assets/css/create_application.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

</head>
<body>
<br><br>
<div class="main-wrapper">
    <div class="container-fluid mt-4">
        <div class="row justify-content-center">
            <div class="col-lg-10 col-md-12">
                <div class="card shadow-lg p-4 rounded-4">
                    <h3 class="text-center text-primary">
                        <i class="bi bi-file-earmark-text"></i> Add New Intellectual Property
                    </h3>
                    <hr>
                    <form id="addEntryForm" action="add_entry.php" method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Title:</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Authors:</label>
                                <div id="authors-container">
                                    <input type="text" name="authors[]" class="form-control mb-2" required>
                                    <button type="button" class="btn btn-success btn-sm" onclick="addAuthor()">+ Add Author</button>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Authors Email:</label>
                                <input type="text" name="email" class="form-control" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label">Applicant Name:</label>
                                <input type="text" name="applicant_name" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Classification:</label>
                                <select name="classification" class="form-select" required>
                                    <option value="Copyright">Copyright</option>
                                    <option value="Patent">Patent</option>
                                    <option value="Trademark">Trademark</option>
                                    <option value="Utility Model">Utility Model</option>
                                    <option value="Industrial Design">Industrial Design</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Status:</label>
                                <select name="status" class="form-select" required>
                                    <option value="Pending">Pending</option>
                                    <option value="Ongoing">Ongoing</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
<br><br>
                            <div class="col-md-12">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="submitted_to_ipophil" value="1">
                                    <label class="form-check-label"> Submitted to IPOPHIL</label>
                                </div>
                            </div>
<br><br>
                            <div class="col-md-6">
                                <label class="form-label">Application Form:</label>
                                <input type="file" name="application_form" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Endorsement Letter:</label>
                                <input type="file" name="endorsement_letter" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Application Fee:</label>
                                <input type="file" name="application_fee" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Issued Certificate:</label>
                                <input type="file" name="issued_certificate" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Project File:</label>
                                <input type="file" name="project_file" class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date Submitted to ITSO:</label>
                                <input type="date" name="date_submitted_to_itso" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date Submitted to IPOPHIL:</label>
                                <input type="date" name="date_submitted_to_ipophil" class="form-control" value="<?php echo htmlspecialchars($row['date_submitted_to_ipophil']); ?>">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Department:</label>
                                <select name="department" class="form-select" required>
                                    <option value="">--Select Department--</option>
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

                             <br>
                            <div class="col-12 text-center mt-3">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-cloud-upload"></i> Submit
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function addAuthor() {
        let container = document.getElementById("authors-container");
        let input = document.createElement("input");
        input.type = "text";
        input.name = "authors[]";
        input.classList.add("form-control", "mb-2");
        input.required = true;
        container.appendChild(input);
    }

    document.getElementById("addEntryForm").addEventListener("submit", function (e) {
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
