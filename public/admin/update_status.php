<?php
session_start();
require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: complaints.php");
    exit();
}

$allowedStatuses   = ["Pending", "In Progress", "Resolved"];
$allowedCategories = ["Hostel", "Transport", "Canteen", "Academics", "Infrastructure", "Examination", "Other"];

// Keep the admin's current filters after saving
$back = [];
if (in_array($_POST["f_status"] ?? "", $allowedStatuses, true)) {
    $back["status"] = $_POST["f_status"];
}
if (in_array($_POST["f_category"] ?? "", $allowedCategories, true)) {
    $back["category"] = $_POST["f_category"];
}
if (trim($_POST["f_q"] ?? "") !== "") {
    $back["q"] = trim($_POST["f_q"]);
}
$redirect = "complaints.php" . (!empty($back) ? "?" . http_build_query($back) : "");

function flash(string $msg, string $type, string $redirect): void
{
    $_SESSION["flash_message"] = $msg;
    $_SESSION["flash_type"]    = $type;
    header("Location: " . $redirect);
    exit();
}

if (empty($_SESSION["csrf"]) || !hash_equals($_SESSION["csrf"], $_POST["csrf"] ?? "")) {
    flash("Invalid request. Please try again.", "error", $redirect);
}

$complaintId   = (int)($_POST["complaint_id"] ?? 0);
$status        = $_POST["status"] ?? "";
$adminResponse = trim($_POST["admin_response"] ?? "");

if ($complaintId <= 0 || !in_array($status, $allowedStatuses, true)) {
    flash("Invalid complaint or status.", "error", $redirect);
}

if ($status === "Resolved" && $adminResponse === "") {
    flash("Please write a response before marking a complaint as Resolved.", "error", $redirect);
}

try {
    $stmt = $conn->prepare("UPDATE complaints SET status = ?, admin_response = ? WHERE id = ?");
    $stmt->execute([
        $status,
        $adminResponse !== "" ? $adminResponse : null,
        $complaintId
    ]);

    flash("Complaint updated to \"" . $status . "\" successfully.", "success", $redirect);

} catch (PDOException $e) {
    error_log($e->getMessage());
    flash("Unable to update the complaint. Please try again.", "error", $redirect);
}