<?php
include 'db_connect.php';

$title = $_POST['title'];
$authors = $_POST['authors'];
$authorsList = implode(", ", $authors);
$email = $_POST['email'];
$applicant_name = $_POST['applicant_name'];
$classification = $_POST['classification'];
$status = $_POST['status'];
$date_submitted_to_itso = $_POST['date_submitted_to_itso'] ?? null;
$submitted = isset($_POST['submitted_to_ipophil']) ? 1 : 0;
$date_submitted_to_ipophil = $_POST['date_submitted_to_ipophil'] ?? null;
$department = $_POST['department'] ?? '';

function calculateExpiration($classification, $date_submitted_to_ipophil) {
    if (empty($date_submitted_to_ipophil)) {
        return null;
    }

    $years = [
        "Copyright" => 50,
        "Patent" => 20,
        "Trademark" => 10,
        "Utility Model" => 7,
        "Industrial Design" => 5
    ];

    return date('Y-m-d', strtotime("+{$years[$classification]} years", strtotime($date_submitted_to_ipophil)));
}


$expiration_date = calculateExpiration($classification, $date_submitted_to_ipophil);

function uploadFile($fileInput) {
    if (!empty($_FILES[$fileInput]['name'])) {
        $targetDir = "uploads/";
        $fileName = basename($_FILES[$fileInput]["name"]);
        $targetFilePath = $targetDir . $fileName;

        if (move_uploaded_file($_FILES[$fileInput]["tmp_name"], $targetFilePath)) {
            return $fileName;
        }
    }
    return NULL;
}

$application_form = uploadFile("application_form");
$endorsement_letter = uploadFile("endorsement_letter");
$application_fee = uploadFile("application_fee");
$issued_certificate = uploadFile("issued_certificate");
$project_file = uploadFile("project_file");

$sql = "INSERT INTO intellectual_properties 
        (title, authors, email, applicant_name, classification, endorsement_letter, status, application_form, submitted, application_fee, issued_certificate, project_file, date_submitted_to_itso, date_submitted_to_ipophil, expiration_date, department) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssisssssss", $title, $authorsList, $email, $applicant_name, $classification, $endorsement_letter, $status, $application_form, $submitted, $application_fee, $issued_certificate, $project_file, $date_submitted_to_itso, $date_submitted_to_ipophil, $expiration_date, $department);

if ($stmt->execute()) {
    echo "<script>alert('Added successfully!'); window.location.href='index.php?page=create_application';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
