<?php
require 'dompdf/vendor/autoload.php';
use Dompdf\Dompdf;

if (isset($_POST['ip_id'])) {
    $ip_id = $_POST['ip_id'];

    include __DIR__ . '/db_connect.php';

    if (!$conn) {
        die("Database connection failed.");
    }

    $query = "SELECT * FROM intellectual_properties WHERE ip_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $ip_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $ip = $result->fetch_assoc();

    if (!$ip) {
        die("No IP record found.");
    }

    function formatDate($date) {
        return ($date && $date != '0000-00-00') ? date("F j, Y", strtotime($date)) : 'N/A';
    }

    $html = '
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; }
            h1 { text-align: center; }
            table { width: 100%; border-collapse: collapse; margin-top: 20px; }
            td { padding: 8px; vertical-align: top; }
            .label { font-weight: bold; width: 30%; }
        </style>
    </head>
    <body>
        <h1>Intellectual Property Details</h1>
        <table>
            <tr><td class="label">IP#:</td><td>' . $ip['ip_id'] . '</td></tr>
            <tr><td class="label">Department:</td><td>' . htmlspecialchars($ip['department']) . '</td></tr>
            <tr><td class="label">Title:</td><td>' . htmlspecialchars($ip['title']) . '</td></tr>
            <tr><td class="label">Authors:</td><td>' . htmlspecialchars($ip['authors']) . '</td></tr>
            <tr><td class="label">Classification:</td><td>' . htmlspecialchars($ip['classification']) . '</td></tr>
            <tr><td class="label">Status:</td><td>' . htmlspecialchars($ip['status']) . '</td></tr>
            <tr><td class="label">Submitted to IPOPHIL:</td><td>' . ($ip['submitted'] ? 'Yes' : 'No') . '</td></tr>
            <tr><td class="label">Date Submitted:</td><td>' . formatDate($ip["date_submitted_to_ipophil"]) . '</td></tr>
            <tr><td class="label">Expiration Date:</td><td>' . formatDate($ip["expiration_date"]) . '</td></tr>';

    $files = [
        "Endorsement Letter" => $ip["endorsement_letter"],
        "Application Fee" => $ip["application_fee"],
        "Issued Certificate" => $ip["issued_certificate"],
        "Project File" => $ip["project_file"]
    ];

    foreach ($files as $label => $file) {
        $html .= '<tr><td class="label">' . $label . ':</td><td>' .
                 ($file ? ' ' . htmlspecialchars($file) : 'Not uploaded') .
                 '</td></tr>';
    }

    $html .= '</table></body></html>';

    $dompdf = new Dompdf();
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $dompdf->stream('IP_Details_' . $ip_id . '.pdf', ['Attachment' => false]); 
} else {
    echo "No IP selected.";
}
?>
