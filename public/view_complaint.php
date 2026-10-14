<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$pageTitle  = "My Complaints - CampusCare";
$complaints = [];
$error      = "";

try {
    $stmt = $conn->prepare(
        "SELECT id, reference_id, category, subject, description, priority,
                status, admin_response, created_at
         FROM complaints
         WHERE user_id = ?
         ORDER BY created_at DESC"
    );
    $stmt->execute([$_SESSION["user_id"]]);
    $complaints = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Unable to load complaints.";
}

$statusClass = ["Pending" => "pending", "In Progress" => "progress", "Resolved" => "resolved"];

include "includes/header.php";
?>

<section class="dashboard-section">
    <div class="container">

        <div class="dashboard-header">
            <div class="section-heading" style="margin:0">
                <span>Complaint Management</span>
                <h2>My Complaints</h2>
                <p>View your submitted complaints and the admin's responses.</p>
            </div>
            <a href="complaint.php" class="primary-button">+ New Complaint</a>
        </div>

        <?php if ($error !== ""): ?>
            <div class="form-message error"><?php echo htmlspecialchars($error); ?></div>

        <?php elseif (empty($complaints)): ?>
            <div class="status-overview empty-state">
                <h3>No Complaints Found</h3>
                <p>You have not submitted any complaints yet.</p>
                <br>
                <a href="complaint.php" class="primary-button">Create Your First Complaint</a>
            </div>

        <?php else: ?>
            <div class="complaint-list">
                <?php foreach ($complaints as $c): ?>
                    <article class="complaint-card">

                        <div class="complaint-card-top">
                            <div>
                                <small class="ref-text"><?php echo htmlspecialchars($c["reference_id"] ?? "N/A"); ?></small>
                                <h3><?php echo htmlspecialchars($c["subject"]); ?></h3>
                            </div>
                            <span class="status-badge <?php echo $statusClass[$c["status"]] ?? "pending"; ?>">
                                <?php echo htmlspecialchars($c["status"]); ?>
                            </span>
                        </div>

                        <div class="complaint-meta">
                            <span>📂 <?php echo htmlspecialchars($c["category"]); ?></span>
                            <span class="priority-badge <?php echo strtolower($c["priority"] ?? ""); ?>">
                                <?php echo htmlspecialchars($c["priority"] ?? "N/A"); ?> priority
                            </span>
                            <span>🗓 <?php echo date("d M Y, h:i A", strtotime($c["created_at"])); ?></span>
                        </div>

                        <p class="complaint-desc"><?php echo nl2br(htmlspecialchars($c["description"])); ?></p>

                        <div class="response-box <?php echo empty($c["admin_response"]) ? "no-response" : ""; ?>">
                            <strong>Admin Response</strong>
                            <p>
                                <?php
                                echo !empty($c["admin_response"])
                                    ? nl2br(htmlspecialchars($c["admin_response"]))
                                    : "No response yet. The administration will review your complaint soon.";
                                ?>
                            </p>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php include "includes/footer.php"; ?>