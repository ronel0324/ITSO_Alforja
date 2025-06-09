<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connect.php';

if ($_SESSION['role'] !== 'Admin') {
    die("Access denied.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE users SET status = 'approved' WHERE id = ?");
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE users SET status = 'rejected' WHERE id = ?");
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    }

    if (isset($stmt)) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Manager</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin: 20px auto; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; margin-top: 40px; }
        form { display: inline; }

button {
    background-color: #4CAF50; 
    color: white; 
    padding: 10px 20px; 
    border: none; 
    border-radius: 5px; 
    cursor: pointer; 
    transition: background-color 0.3s ease; 
    font-size: 16px; 
}

button:hover {
    background-color: #45a049;
}

button.delete {
    background-color: #f44336;
}

button.delete:hover {
    background-color: #e53935; 
}

button.cancel {
    background-color: #f1f1f1;
    color: #555; 

button.cancel:hover {
    background-color: #ddd; 
}

#editModal button {
    margin-top: 10px; 
    width: 100%; 
    padding: 12px; 
}

#editModal button.cancel {
    background-color: #ccc;
}

    </style>
</head>
<body>

<h2>Pending User Accounts</h2>
<table>
    <tr>
        <th>ID</th><th>Username</th><th>Department</th><th>Role</th><th>Status</th><th>Actions</th>
    </tr>
    <?php
    $pending = $conn->query("SELECT * FROM users WHERE status = 'pending' AND role != 'Admin'");
    while ($user = $pending->fetch_assoc()):
    ?>
    <tr>
        <td><?= $user['id']; ?></td>
        <td><?= htmlspecialchars($user['username']); ?></td>
        <td><?= $user['department']; ?></td>
        <td><?= $user['role']; ?></td>
        <td><?= $user['status']; ?></td>
        <td>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <input type="hidden" name="action" value="approve">
                <button type="submit">Approve</button>
            </form>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <input type="hidden" name="action" value="reject">
                <button type="submit">Reject</button>
            </form>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" onclick="return confirm('Delete this user?')">Delete</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<br><br><br>

<h2>Approved Accounts</h2>
<table>
    <tr>
        <th>ID</th><th>Username</th><th>Department</th><th>Role</th><th>Actions</th>
    </tr>
    <?php
    $approved = $conn->query("SELECT * FROM users WHERE status = 'approved' AND role != 'Admin'");
    while ($user = $approved->fetch_assoc()):
    ?>
    <tr>
        <td><?= $user['id']; ?></td>
        <td><?= htmlspecialchars($user['username']); ?></td>
        <td><?= $user['department']; ?></td>
        <td><?= $user['role']; ?></td>
        <td>
            <form action="edit_account.php" method="GET">
                <input type="hidden" name="id" value="<?= $user['id']; ?>">
                <button type="button" onclick="openModal('<?= $user['id']; ?>', '<?= $user['username']; ?>')">Edit</button>
            </form>
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $user['id']; ?>">
                <input type="hidden" name="action" value="delete">
                <button type="submit" class="delete" onclick="return confirm('Delete this user?')">Delete</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
</table>

<div id="editModal" class="modal" style="display:none; position:fixed; top:0; left:0; 
    width:100%; height:100%; background:rgba(0,0,0,0.5); justify-content:center; align-items:center;">
    <div style="background:#fff; padding:20px; width:300px; border-radius:8px; position:relative;">
        <h3>Edit Account</h3>
        <form method="POST" action="update_account.php">
            <input type="hidden" name="id" id="editId">
            
            <label>Username:</label>
            <input type="text" name="username" id="editUsername" required><br><br>

            <label>New Password:</label>
            <input type="password" name="password"><br><br>

            <button type="submit">Update</button>
            <button type="button" class="cancel" onclick="closeModal()">Cancel</button>
        </form>
    </div>
</div>

<script>
    function openModal(id, username) {
        document.getElementById("editId").value = id;
        document.getElementById("editUsername").value = username;
        document.getElementById("editModal").style.display = "flex";
    }

    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }

    window.onclick = function(event) {
        const modal = document.getElementById("editModal");
        if (event.target === modal) {
            closeModal();
        }
    }
</script>


</body>
</html>
