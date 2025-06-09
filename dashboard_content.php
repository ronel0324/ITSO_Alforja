<div class="topbar">
    <div class="search">
        <label>
            <input type="text" placeholder="Search here">
            <ion-icon name="search-outline"></ion-icon>
        </label>
    </div>
</div>

<div class="cardBox">
    <!-- Completed -->
    <div class="card completed">
        <div>
            <div class="numbers"><?php echo $approved_count; ?></div>
            <div class="cardName">Completed Application</div>
            <canvas id="chartCompleted" width="200" height="5"></canvas>
        </div>
        <div class="iconBx">
            <ion-icon name="checkmark-circle-outline"></ion-icon>
        </div>
    </div>

    <!-- Pending -->
    <div class="card pending">
        <div>
            <div class="numbers"><?php echo $rejected_count; ?></div>
            <div class="cardName">Pending Application</div>
            <canvas id="chartPending" width="200" height="5"></canvas>
        </div>
        <div class="iconBx">
            <ion-icon name="alert-circle-outline"></ion-icon>
        </div>
    </div>

    <!-- Ongoing -->
    <div class="card ongoing">
        <div>
            <div class="numbers"><?php echo $ongoing_count; ?></div>
            <div class="cardName">Ongoing Application</div>
            <canvas id="chartOngoing" width="200" height="5"></canvas>
        </div>
        <div class="iconBx">
            <ion-icon name="time-outline"></ion-icon>
        </div>
    </div>

    <!-- All Applications -->
    <div class="card all">
        <div>
            <div class="numbers"><?php echo $all_count; ?></div>
            <div class="cardName">All Application Summary</div>
            <canvas id="chartAll" width="200" height="5"></canvas>
        </div>
        <div class="iconBx">
            <ion-icon name="file-tray-full-outline"></ion-icon>
        </div>
    </div>
</div>

<!-- Table Section -->
<div class="details">
    <div class="recentEntry">

        <form method="GET" action="" style="margin-bottom: 10px;">
            <label for="status">Filter by Status:</label>
            <select name="status" onchange="this.form.submit()" style="padding: 5px;">
                <option value="">-- Show All --</option>
                <option value="Completed" <?php if(isset($_GET['status']) && $_GET['status'] == 'Completed') echo 'selected'; ?>>Completed</option>
                <option value="Pending" <?php if(isset($_GET['status']) && $_GET['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                <option value="Ongoing" <?php if(isset($_GET['status']) && $_GET['status'] == 'Ongoing') echo 'selected'; ?>>Ongoing</option>
            </select>
        </form>

        <h3 class="mt-4">All Intellectual Properties</h3>
        <div style="overflow-x: auto; width: 100%; height: 600px; border-bottom: 1px solid #ccc; position: relative; z-index: 10;">
    <div style="min-width: 1500px;">
        <table class="table table-bordered mt-3">
                <thead style="position: sticky; top: 0; background: white; z-index: 10;">
                    <tr>
                        <th>IP#</th>
                        <th>Department</th>
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
                    <?php
                    $statusFilter = isset($_GET['status']) ? $_GET['status'] : '';

                    $sql = "SELECT * FROM intellectual_properties";
                    if (!empty($statusFilter)) {
                        $sql .= " WHERE status = '$statusFilter'";
                    }
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $status_class = '';
                            switch (strtolower($row['status'])) {
                                case 'completed': $status_class = 'status-completed'; break;
                                case 'pending': $status_class = 'status-pending'; break;
                                case 'ongoing': $status_class = 'status-ongoing'; break;
                            }

                            echo "<tr>";    
                            echo "<td>" . $row["ip_id"] . "</td>";
                            echo "<td>" . $row["department"] . "</td>";
                            echo "<td>" . $row["title"] . "</td>";
                            echo "<td>" . $row["authors"] . "</td>";
                            echo "<td>" . $row["applicant_name"] . "</td>";
                            echo "<td>" . $row["classification"] . "</td>";
                            echo "<td>" . generateFileLink($row["endorsement_letter"]) . "</td>";
                            echo "<td><span class='status-badge $status_class'>" . $row["status"] . "</span></td>";
                            echo "<td>" . generateFileLink($row["application_form"]) . "</td>";
                            echo "<td>" . ($row["submitted"] ? 'Yes' : 'No') . "</td>";
                            echo "<td>" . generateFileLink($row["application_fee"]) . "</td>";
                            echo "<td>" . generateFileLink($row["issued_certificate"]) . "</td>";
                            echo "<td>" . generateFileLink($row["project_file"]) . "</td>";
                            echo "<td>" . date("F j, Y", strtotime($row["date_submitted_to_itso"])) . "</td>";
                            $date = $row["date_submitted_to_ipophil"];
                            echo "<td>" . (!empty($date) && $date != '0000-00-00' ? date("F j, Y", strtotime($date)) : 'N/A') . "</td>";
                            
                            echo "<td>" . (!empty($row["expiration_date"]) && $row["expiration_date"] != '0000-00-00' ? date("F j, Y", strtotime($row["expiration_date"])) : 'N/A') . "</td>";
                            echo '<td>
                                <form action="update.php" method="get">
                                    <input type="hidden" name="ip_id" value="' . $row["ip_id"] . '">
                                    <button type="submit" class="update-badge">
                                        <ion-icon name="create-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>Update
                                    </button>
                                </form>
<br>
                                <form action="generate_pdf.php" method="post" target="_blank">
                                    <input type="hidden" name="ip_id" value="' . $row["ip_id"] . '">
                                    <button type="submit" class="generate-badge">
                                        <ion-icon name="document-text-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>Generate
                                    </button>
                                </form>
<br>
                                <form action="delete.php" method="post" onsubmit="return confirm(\'Are you sure you want to delete this record?\')" style="display: inline-block;">
                                    <input type="hidden" name="ip_id" value="' . $row["ip_id"] . '">
                                    <button type="submit" class="delete-badge" style="color: red;">
                                        <ion-icon name="trash-outline" style="vertical-align: middle; margin-right: 5px;"></ion-icon>Delete
                                    </button>
                                </form>
                            </td>';

                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='16'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const searchInput = document.querySelector(".search input");
searchInput.addEventListener("keyup", function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll(".table tbody tr");

    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(filter) ? "" : "none";
    });
});

</script>
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>
