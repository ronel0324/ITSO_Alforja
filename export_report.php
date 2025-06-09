<?php
require 'dompdf/autoload.inc.php';
use Dompdf\Dompdf;

include 'db_connect.php';

require_once 'jpgraph/src/jpgraph.php';
require_once 'jpgraph/src/jpgraph_pie.php';
require_once 'jpgraph/src/jpgraph_pie3d.php';

$departmentNames = [
    'CCS' => 'College of Computer Studies',
    'CFND' => 'College of Food Nutrition and Dietetics',
    'CIT' => 'College of Industrial Technology',
    'CTE' => 'College of Teacher Education',
    'CA' => 'College of Agriculture',
    'CAS' => 'College of Arts and Sciences',
    'CBAA' => 'College of Business Administration and Accountancy',
    'COE' => 'College of Engineering',
    'CCJE' => 'College of Criminal Justice Education',
    'COF' => 'College of Fisheries',
    'CHMT' => 'College of Hospitality Management and Tourism',
    'CNAH' => 'College of Nursing and Allied Health',
];

$chartSQL = "SELECT status, COUNT(*) as total 
             FROM intellectual_properties 
             WHERE date_submitted_to_itso >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
             GROUP BY status";
$chartResult = $conn->query($chartSQL);

$statusCounts = [
    'Pending' => 0,
    'Ongoing' => 0,
    'Completed' => 0
];

while ($row = $chartResult->fetch_assoc()) {
    $status = $row['status'];
    if (isset($statusCounts[$status])) {
        $statusCounts[$status] = (int)$row['total'];
    }
}

$data = array_values($statusCounts);
$labels = array_keys($statusCounts);


$graph = new PieGraph(350, 250);
$graph->SetShadow();
$graph->title->SetFont(FF_FONT1, FS_BOLD);

$p1 = new PiePlot3D($data);
$p1->SetLegends($labels);
$p1->SetCenter(0.5);
$p1->SetSize(0.3);


$statusColors = [
    'Pending' => 'DC3545',    // red
    'Ongoing' => 'FFC107',    // yellow
    'Completed' => '28A745',  // green
];

$sliceColors = [];
foreach ($labels as $label) {
    $sliceColors[] = $statusColors[$label] ?? 'CCCCCC'; 
}
$p1->SetSliceColors($sliceColors);
$p1->value->SetFormat('%d');  
$p1->value->Show();
$p1->value->SetFormat('%.1f%%'); 
$p1->value->Show();
$graph->Add($p1);

$legendLabels = [];
foreach ($labels as $i => $label) {
    $legendLabels[] = $label . ' (' . $data[$i] . ')';
}
$p1->SetLegends($legendLabels);


ob_start();
$graph->Stroke(_IMG_HANDLER);
$imageHandler = $graph->img;
$imageHandler->Stream();
$imageData = ob_get_contents();
ob_end_clean();

$base64Image = base64_encode($imageData);

$sql = "SELECT * FROM intellectual_properties 
        WHERE date_submitted_to_itso >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
        ORDER BY department, classification, date_submitted_to_itso DESC";
$result = $conn->query($sql);

$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $dept = $row['department'];
    $class = $row['classification'];
    $groupedData[$dept][$class][] = $row;
}

$html = "
<style>
  @font-face {
    font-family: 'OldeEnglish';
    src: url('http://localhost/itso_tracking_system/fonts/OldeEnglish.ttf') format('truetype');
  }

  .header-container {
    width: 100%;
    margin-bottom: 20px;
  }

  .logo {
    width: 80px;
    height: auto;
  }

  .logo1 {
    width: 55px;
    height: auto;
  }

  .gov-header {
    font-family: Arial, sans-serif;
    font-size: 16px;
    text-align: center;
    margin: 0;
  }

  .sub-header {
    font-family: 'OldeEnglish', cursive;
    font-size: 28px;
    text-align: center;
    margin: 0 0 5px 0;
    font-weight: bold;
  }

  .univ-header {
    font-family: 'OldeEnglish', cursive;
    font-size: 24px;
    text-align: center;
    margin: 0;
  }

  h1, h3, h4 {
    font-family: Arial, sans-serif;
    margin: 10px 0;
  }

  h1 { font-size: 16px; text-align: center; }
  h3 { font-size: 16px; }
  h4 { font-size: 12px; }

  table {
    border-collapse: collapse;
    width: 100%;
    margin-bottom: 30px;
    font-family: Arial, sans-serif;
  }

  th, td {
    border: 1px solid #000;
    padding: 5px;
    font-size: 12px;
  }

  thead {
    background-color: #f2f2f2;
  }
</style>

<div class='header-container'>
  <table style='width: 100%; margin-bottom: 10px;'>
    <tr>
      <td style='width: 80px; text-align: center;'>
        <img src='http://localhost/itso_tracking_system/assets/imgs/logo.png' class='logo'>
      </td>
      <td style='text-align: left; padding-left: 10px;'>
        <p class='gov-header'>Republic of the Philippines</p>
        <p class='sub-header'>Laguna State Polytechnic University</p>
        <p class='gov-header'>Province of Laguna</p>
      </td>
      <td style='width: 80px; text-align: center;'>
        <img src='http://localhost/itso_tracking_system/assets/imgs/bglogo.png' class='logo1'>
      </td>
    </tr>
  </table>

  <h1>Innovation Technology Support Office</h1>

  <div style='text-align:center; margin-bottom:30px;'>
    <img src='data:image/png;base64,$base64Image' style='max-width:40%;' alt='Status Chart'>
  </div>
</div>
";

foreach ($groupedData as $dept => $classifications) {
    $fullDeptName = $departmentNames[$dept] ?? $dept;
    $html .= "<h3>" . htmlspecialchars($fullDeptName) . "</h3>";
    foreach ($classifications as $class => $records) {
        $html .= "<h4>" . htmlspecialchars($class) . "</h4>";
        $html .= "<table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Classification</th>
                            <th>Department</th>
                            <th>Authors</th>
                            <th>Status</th>
                            <th>Filing Date</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($records as $row) {
            $fullDept = $departmentNames[$row['department']] ?? $row['department'];
            $html .= "<tr>
                        <td>" . htmlspecialchars($row['ip_id']) . "</td>
                        <td>" . htmlspecialchars($row['title']) . "</td>
                        <td>" . htmlspecialchars($row['classification']) . "</td>
                        <td>" . htmlspecialchars($fullDept) . "</td>
                        <td>" . htmlspecialchars($row['authors']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>" . date("F j, Y", strtotime($row['date_submitted_to_itso'])) . "</td>
                      </tr>";
        }
        $html .= "</tbody></table>";
    }
}

$options = new \Dompdf\Options();
$options->set('defaultFont', 'OldeEnglish');
$options->set('isRemoteEnabled', true); 

$dompdf = new Dompdf($options);

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("ip_monitoring_report.pdf", ["Attachment" => false]);
?>
