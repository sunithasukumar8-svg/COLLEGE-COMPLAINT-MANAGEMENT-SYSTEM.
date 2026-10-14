<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit();
}

$pageTitle = "Admin Dashboard - CampusCare";
$basePath  = "../";

$counts = ["Pending" => 0, "In Progress" => 0, "Resolved" => 0];
$total = 0;
$byCategory = [];
$recentPending = [];
$error = "";

try {
    foreach ($conn->query("SELECT status, COUNT(*) AS n FROM complaints GROUP BY status") as $row) {
        if (isset($counts[$row["status"]])) {
            $counts[$row["status"]] = (int)$row["n"];
        }
        $total += (int)$row["n"];
    }

    $byCategory = $conn->query(
        "SELECT category, COUNT(*) AS n FROM complaints GROUP BY category ORDER BY n DESC"
    )->fetchAll();

    $recentPending = $conn->query(
        "SELECT c.id, c.reference_id, c.category, c.subject, c.priority, c.created_at, u.name
         FROM complaints c
         JOIN users u ON u.id = c.user_id
         WHERE c.status = 'Pending'
         ORDER BY FIELD(c.priority, 'High', 'Medium', 'Low'), c.created_at ASC
         LIMIT 5"
    )->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Unable to load dashboard data.";
}

$resolvedPercent = $total > 0 ? round(($counts["Resolved"] / $total) * 100) : 0;
$maxCat = !empty($byCategory) ? max(array_column($byCategory, "n")) : 1;

include "../includes/header.php";
?>

<section class="dashboard-section">
    <div class="container">

        <div class="dashboard-header">
            <div class="section-heading" style="margin:0">
                <span>Administration</span>
                <h2>Welcome, <?php echo htmlspecialchars($_SESSION["name"] ?? "Admin"); ?></h2>
                <p>Review, respond to and resolve student complaints.</p>
            </div>
            <a href="complaints.php?status=Pending" class="primary-button">Review Pending</a>
        </div>

        <?php if ($error !== ""): ?>
            <div class="form-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <div class="dashboard-grid">
            <div class="dashboard-card"><h3>Total</h3><div class="dashboard-number"><?php echo $total; ?></div></div>
            <div class="dashboard-card"><h3>Pending</h3><div class="dashboard-number"><?php echo $counts["Pending"]; ?></div></div>
            <div class="dashboard-card"><h3>In Progress</h3><div class="dashboard-number"><?php echo $counts["In Progress"]; ?></div></div>
            <div class="dashboard-card"><h3>Resolved</h3><div class="dashboard-number"><?php echo $counts["Resolved"]; ?></div></div>
        </div>

        <div class="admin-two-col">

            <div class="status-overview">
                <h3>Resolution Rate</h3>
                <div class="progress-track">
                    <div class="progress-fill" style="width: <?php echo $resolvedPercent; ?>%"></div>
                </div>
                <p class="muted-text"><?php echo $resolvedPercent; ?>% of all complaints are resolved.</p>

                <h3 style="margin-top:28px">By Category</h3>
                <?php if (empty($byCategory)): ?>
                    <p class="muted-text">No data yet.</p>
                <?php else: ?>
                    <?php foreach ($byCategory as $cat): ?>
                        <div class="bar-row">
                            <span><?php echo htmlspecialchars($cat["category"]); ?></span>
                            <div class="bar-track">
                                <div class="bar-fill" style="width: <?php echo round(($cat["n"] / $maxCat) * 100); ?>%"></div>
                            </div>
                            <strong><?php echo (int)$cat["n"]; ?></strong>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="status-overview">
                <h3>Needs Attention</h3>
                <p class="muted-text">Highest-priority pending complaints first.</p>

                <?php if (empty($recentPending)): ?>
                    <div class="empty-state"><p>🎉 No pending complaints. All caught up!</p></div>
                <?php else: ?>
                    <ul class="pending-list">
                        <?php foreach ($recentPending as $p): ?>
                            <li>
                                <div>
                                    <small class="ref-text"><?php echo htmlspecialchars($p["reference_id"]); ?></small>
                                    <strong><?php echo htmlspecialchars($p["subject"]); ?></strong>
                                    <span class="muted-text">
                                        <?php echo htmlspecialchars($p["name"]); ?> &middot;
                                        <?php echo htmlspecialchars($p["category"]); ?> &middot;
                                        <?php echo date("d M", strtotime($p["created_at"])); ?>
                                    </span>
                                </div>
                                <span class="priority-badge <?php echo strtolower($p["priority"]); ?>">
                                    <?php echo htmlspecialchars($p["priority"]); ?>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="complaints.php?status=Pending" class="secondary-button" style="margin-top:18px">
                        View all pending
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </div>
</section>

<?php include "../includes/footer.php"; ?>