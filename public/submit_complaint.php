<?php
session_start();
require_once "config/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

$allowedCategories = ["Hostel", "Transport", "Canteen", "Academics", "Infrastructure", "Examination", "Other"];
$allowedPriorities = ["Low", "Medium", "High"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $category    = trim($_POST["category"] ?? "");
    $subject     = trim($_POST["subject"] ?? "");
    $priority    = trim($_POST["priority"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (
        $subject === "" || $description === "" ||
        !in_array($category, $allowedCategories, true) ||
        !in_array($priority, $allowedPriorities, true)
    ) {
        $_SESSION["flash_message"] = "Please fill in all complaint details correctly.";
        $_SESSION["flash_type"]    = "error";
        header("Location: complaint.php");
        exit();
    }

    $referenceId = "CC-" . date("Ymd") . "-" . strtoupper(bin2hex(random_bytes(3)));

    try {
        $stmt = $conn->prepare(
            "INSERT INTO complaints
             (user_id, reference_id, category, subject, description, priority, status, admin_response, created_at)
             VALUES (?, ?, ?, ?, ?, ?, 'Pending', NULL, NOW())"
        );
        $stmt->execute([
            $_SESSION["user_id"],
            $referenceId,
            $category,
            mb_substr($subject, 0, 150),
            $description,
            $priority
        ]);
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION["flash_message"] = "Unable to submit complaint. Please try again.";
        $_SESSION["flash_type"]    = "error";
        header("Location: complaint.php");
        exit();
    }

    // Redirect so refreshing the page doesn't submit the complaint twice
    $_SESSION["last_complaint"] = [
        "reference" => $referenceId,
        "category"  => $category,
        "subject"   => $subject,
        "priority"  => $priority,
    ];
    header("Location: submit_complaint.php");
    exit();
}

$done = $_SESSION["last_complaint"] ?? null;
unset($_SESSION["last_complaint"]);

if (!$done) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "Complaint Submitted - CampusCare";
include "includes/header.php";
?>

<section class="form-section">
    <div class="container">
        <div class="form-card">

            <div class="section-heading">
                <span>Complaint Submitted</span>
                <h2>Your Complaint Has Been Received</h2>
                <p>Please keep your reference ID for future tracking.</p>
            </div>

            <div class="reference-box">
                <span>Complaint Reference ID</span>
                <strong><?php echo htmlspecialchars($done["reference"]); ?></strong>
            </div>

            <div class="complaint-summary">
                <div><strong>Category</strong><p><?php echo htmlspecialchars($done["category"]); ?></p></div>
                <div><strong>Subject</strong><p><?php echo htmlspecialchars($done["subject"]); ?></p></div>
                <div><strong>Priority</strong><p><?php echo htmlspecialchars($done["priority"]); ?></p></div>
                <div><strong>Status</strong><p>Pending</p></div>
            </div>

            <div class="form-actions">
                <a href="complaint.php" class="secondary-button">Submit Another</a>
                <a href="view_complaint.php" class="primary-button">View My Complaints</a>
            </div>

        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>