<?php
include 'db_connect.php';
include 'includes/utils.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
if (isset($_GET['ip_id']) && isset($_POST['update'])) {
    $ip_id = $_GET['ip_id'];

    $title = $_POST['title'];
    $recipient_email = $_POST['email'];
    $status = $_POST['status'];
    $submitted = isset($_POST['submitted']) ? 1 : 0;
    $classification = $_POST['classification'];
    $date_submitted_to_ipophil = $_POST['date_submitted_to_ipophil'];
    $date_submitted_to_itso = isset($_POST['date_submitted_to_itso']) ? $_POST['date_submitted_to_itso'] : '';

    function uploadFile($field_name, $current_file) {
        if (!empty($_FILES[$field_name]['name'])) {
            $file_name = time() . "_" . basename($_FILES[$field_name]["name"]);
            move_uploaded_file($_FILES[$field_name]["tmp_name"], "uploads/" . $file_name);
            return $file_name;
        }
        return $current_file;
    }

    $result = mysqli_query($conn, "SELECT * FROM intellectual_properties WHERE ip_id = $ip_id");
    $existing = mysqli_fetch_assoc($result);

    $status_changed = ($existing['status'] !== $status);
    $recipient_email = $existing['email'];

    $endorsement_letter = uploadFile('endorsement_letter', $existing['endorsement_letter']);
    $application_form = uploadFile("application_form", $existing["application_form"]);
    $application_fee = uploadFile('application_fee', $existing['application_fee']);
    $issued_certificate = uploadFile('issued_certificate', $existing['issued_certificate']);
    $project_file = uploadFile('project_file', $existing['project_file']);

    $expiration_date = calculateExpiration($classification, $date_submitted_to_ipophil);

    $sql = "UPDATE intellectual_properties SET
        title='$title',
        status='$status',
        submitted='$submitted',
        endorsement_letter='$endorsement_letter',
        application_form='$application_form',
        application_fee='$application_fee',
        issued_certificate='$issued_certificate',
        project_file='$project_file',
        expiration_date='$expiration_date',
        classification='$classification',
        date_submitted_to_ipophil='$date_submitted_to_ipophil'";

    if (!empty($date_submitted_to_itso)) {
        $sql .= ", date_submitted_to_itso='$date_submitted_to_itso'";
    }

    $sql .= " WHERE ip_id=$ip_id";

     if (mysqli_query($conn, $sql)) {
        if ($status_changed && !empty($recipient_email)) {
            $mail = new PHPMailer(true); 

            /* Optional po kung trip nyong nag nonotif yung update ng status dun sa gmail ng author */
            try {
                $mail->isSMTP(); 
                $mail->Host = 'smtp.gmail.com'; 
                $mail->SMTPAuth = true; 
                $mail->Username = '@gmail.com'; // your email address
                $mail->Password = ''; // SMTP password (use an App Password if using Gmail)
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
                $mail->Port = 587; 

                //Recipients
                $mail->setFrom('no-reply@example.com', 'Innovation Technology Support Office'); 
                $mail->addAddress($recipient_email); 

                $mail->isHTML(true);
                $mail->Subject = 'Status Update for IP Record: ' . $title;
                $mail->Body    = "
                <html>
                <head><title>Status Update</title></head>
                <body>
                <p>Hello,</p>
                <p>The status for your Intellectual Property record has been updated:</p>
                <ul>
                    <li><strong>Title:</strong> $title</li>
                    <li><strong>Old Status:</strong> {$existing['status']}</li>
                    <li><strong>New Status:</strong> $status</li>
                </ul>
                <p>Regards,<br>Innovation Technology Support Office</p>
                </body>
                </html>
                ";

                if ($mail->send()) {
                    echo "Email sent successfully!";
                } else {
                    echo "Mailer Error: " . $mail->ErrorInfo;
                }
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        } 

        header("Location: admin_dashboard.php?updated=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
