<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit();
}

if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(32));
}

$pageTitle = "Manage Complaints - CampusCare";
$basePath  = "../";

$statuses    = ["Pending", "In Progress", "Resolved"];
$categories  = ["Hostel", "Transport", "Canteen", "Academics", "Infrastructure", "Examination", "Other"];
$statusClass = ["Pending" => "pending", "In Progress" => "progress", "Resolved" => "resolved"];

$fStatus   = $_GET["status"] ?? "";
$fCategory = $_GET["category"] ?? "";
$fSearch   = trim($_GET["q"] ?? "");

if (!in_array($fStatus, $statuses, true))     { $fStatus = ""; }
if (!in_array($fCategory, $categories, true)) { $fCategory = ""; }

$where  = [];
$params = [];

if ($fStatus !== "")   { $where[] = "c.status = ?";   $params[] = $fStatus; }
if ($fCategory !== "") { $where[] = "c.category = ?"; $params[] = $fCategory; }
if ($fSearch !== "") {
    $where[] = "(c.subject LIKE ? OR c.reference_id LIKE ? OR u.name LIKE ?)";
    $like = "%" . $fSearch . "%";
    array_push($params, $like, $like, $like);
}

$sql = "SELECT c.id, c.reference_id, c.category, c.subject, c.description,
               c.priority, c.status, c.admin_response, c.created_at,
               u.name AS student_name, u.email AS student_email
        FROM complaints c
        JOIN users u ON u.id = c.user_id"
     . (!empty($where) ? " WHERE " . implode(" AND ", $where) : "")
     . " ORDER BY FIELD(c.status, 'Pending', 'In Progress', 'Resolved'),
                  FIELD(c.priority, 'High', 'Medium', 'Low'),
                  c.created_at DESC";

$complaints = [];
$error = "";

try {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $complaints = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $error = "Unable to load complaints.";
}

$message     = $_SESSION["flash_message"] ?? "";
$messageType = $_SESSION["flash_type"] ?? "";
unset($_SESSION["flash_message"], $_SESSION["flash_type"]);

include "../includes/header.php";
?>

<section class="dashboard-section">
    <div class="container">

        <div class="dashboard-header">
            <div class="section-heading" style="margin:0">
                <span>Administration</span>
                <h2>Manage Complaints</h2>
                <p><?php echo count($complaints); ?> complaint(s) found.</p>
            </div>
            <a href="dashboard.php" class="secondary-button">&larr; Dashboard</a>
        </div>

        <?php if ($message !== ""): ?>
            <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <?php if ($error !== ""): ?>
            <div class="form-message error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="GET" action="complaints.php" class="filter-bar">
            <input type="text" name="q" placeholder="Search subject, ref ID or student"
                   value="<?php echo htmlspecialchars($fSearch); ?>">

            <select name="status">
                <option value="">All Status</option>
                <?php foreach ($statuses as $s): ?>
                    <option value="<?php echo $s; ?>" <?php echo $fStatus === $s ? "selected" : ""; ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
            </select>

            <select name="category">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat; ?>" <?php echo $fCategory === $cat ? "selected" : ""; ?>><?php echo $cat; ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit" class="primary-button">Filter</button>
            <a href="complaints.php" class="secondary-button">Reset</a>
        </form>

        <?php if (empty($complaints) && $error === ""): ?>
            <div class="status-overview empty-state">
                <h3>No complaints match your filters</h3>
                <p>Try changing the search or filter options.</p>
            </div>
        <?php endif; ?>

        <div class="complaint-list">
            <?php foreach ($complaints as $c): ?>
                <article class="complaint-card">

                    <div class="complaint-card-top">
                        <div>
                            <small class="ref-text"><?php echo htmlspecialchars($c["reference_id"]); ?></small>
                            <h3><?php echo htmlspecialchars($c["subject"]); ?></h3>
                        </div>
                        <span class="status-badge <?php echo $statusClass[$c["status"]] ?? "pending"; ?>">
                            <?php echo htmlspecialchars($c["status"]); ?>
                        </span>
                    </div>

                    <div class="complaint-meta">
                        <span>👤 <?php echo htmlspecialchars($c["student_name"]); ?> (<?php echo htmlspecialchars($c["student_email"]); ?>)</span>
                        <span>📂 <?php echo htmlspecialchars($c["category"]); ?></span>
                        <span class="priority-badge <?php echo strtolower($c["priority"]); ?>">
                            <?php echo htmlspecialchars($c["priority"]); ?> priority
                        </span>
                        <span>🗓 <?php echo date("d M Y, h:i A", strtotime($c["created_at"])); ?></span>
                    </div>

                    <p class="complaint-desc"><?php echo nl2br(htmlspecialchars($c["description"])); ?></p>

                    <form method="POST" action="update_status.php" class="admin-form" novalidate>
                        <input type="hidden" name="csrf" value="<?php echo $_SESSION["csrf"]; ?>">
                        <input type="hidden" name="complaint_id" value="<?php echo (int)$c["id"]; ?>">
                        <input type="hidden" name="f_status" value="<?php echo htmlspecialchars($fStatus); ?>">
                        <input type="hidden" name="f_category" value="<?php echo htmlspecialchars($fCategory); ?>">
                        <input type="hidden" name="f_q" value="<?php echo htmlspecialchars($fSearch); ?>">

                        <div class="admin-form-row">
                            <div class="form-group">
                                <label>Update Status</label>
                                <select name="status" required>
                                    <?php foreach ($statuses as $s): ?>
                                        <option value="<?php echo $s; ?>" <?php echo $c["status"] === $s ? "selected" : ""; ?>><?php echo $s; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="form-group grow">
                                <label>Response to Student</label>
                                <textarea name="admin_response" rows="2"
                                          placeholder="Write a remark or resolution note..."><?php echo htmlspecialchars($c["admin_response"] ?? ""); ?></textarea>
                            </div>
                        </div>

                        <button type="submit" class="primary-button">Save Update</button>
                    </form>

                </article>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<?php include "../includes/footer.php"; ?>