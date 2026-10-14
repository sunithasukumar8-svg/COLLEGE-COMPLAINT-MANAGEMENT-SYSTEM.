<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["role"] ?? "") === "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

$pageTitle = "Dashboard - CampusCare";
$userName  = $_SESSION["name"] ?? "Student";

$complaints = [];
$error = "";

try {
    $stmt = $conn->prepare("SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION["user_id"]]);
    $complaints = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Unable to load your complaints.";
}

$counts = ["Pending" => 0, "In Progress" => 0, "Resolved" => 0];
foreach ($complaints as $c) {
    if (isset($counts[$c["status"]])) {
        $counts[$c["status"]]++;
    }
}

$statusClass = ["Pending" => "pending", "In Progress" => "progress", "Resolved" => "resolved"];

include "includes/header.php";
?>

<section class="dashboard-section">
    <div class="container">

        <div class="dashboard-header">
            <div class="section-heading" style="margin:0">
                <span>Student Dashboard</span>
                <h2>Welcome, <?php echo htmlspecialchars($userName); ?>!</h2>
                <p>Track all your complaints from one place.</p>
            </div>
            <a href="complaint.php" class="primary-button">+ New Complaint</a>
        </div>

        <?php if ($error !== ""): ?>
            <div class="form-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Total</h3>
                <div class="dashboard-number"><?php echo count($complaints); ?></div>
            </div>
            <div class="dashboard-card">
                <h3>Pending</h3>
                <div class="dashboard-number"><?php echo $counts["Pending"]; ?></div>
            </div>
            <div class="dashboard-card">
                <h3>In Progress</h3>
                <div class="dashboard-number"><?php echo $counts["In Progress"]; ?></div>
            </div>
            <div class="dashboard-card">
                <h3>Resolved</h3>
                <div class="dashboard-number"><?php echo $counts["Resolved"]; ?></div>
            </div>
        </div>

        <div class="complaint-table-wrapper">
            <table class="complaint-table">
                <thead>
                    <tr>
                        <th>Ref ID</th>
                        <th>Category</th>
                        <th>Subject</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($complaints)): ?>
                    <tr>
                        <td colspan="6">
                            <div class="empty-state">
                                <p>No complaints yet. Click "New Complaint" to submit one.</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($complaints as $c): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($c["reference_id"] ?? ("#" . $c["id"])); ?></strong></td>
                            <td><?php echo htmlspecialchars($c["category"]); ?></td>
                            <td><?php echo htmlspecialchars($c["subject"]); ?></td>
                            <td>
                                <span class="priority-badge <?php echo strtolower($c["priority"] ?? ""); ?>">
                                    <?php echo htmlspecialchars($c["priority"] ?? "N/A"); ?>
                                </span>
                            </td>
                            <td>
                                <span class="status-badge <?php echo $statusClass[$c["status"]] ?? "pending"; ?>">
                                    <?php echo htmlspecialchars($c["status"]); ?>
                                </span>
                            </td>
                            <td><?php echo date("d M Y", strtotime($c["created_at"])); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <p class="form-footer"><a href="view_complaint.php">View full details &amp; admin responses &rarr;</a></p>

    </div>
</section>

<?php include "includes/footer.php"; ?>