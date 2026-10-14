<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION["role"] ?? "") === "admin") {
    header("Location: admin/dashboard.php");
    exit();
}

$pageTitle = "Submit Complaint - CampusCare";

$message     = $_SESSION["flash_message"] ?? "";
$messageType = $_SESSION["flash_type"] ?? "";
unset($_SESSION["flash_message"], $_SESSION["flash_type"]);

include "includes/header.php";
?>

<section class="form-section">
    <div class="container">
        <div class="form-card">

            <div class="section-heading">
                <span>Student Complaint</span>
                <h2>Submit a Complaint</h2>
                <p>Share the details of your issue so the administration can review it.</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="submit_complaint.php" novalidate>

                <div class="form-group">
                    <label for="category">Complaint Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Hostel">Hostel</option>
                        <option value="Transport">Transport</option>
                        <option value="Canteen">Canteen</option>
                        <option value="Academics">Academics</option>
                        <option value="Infrastructure">Infrastructure</option>
                        <option value="Examination">Examination</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="subject">Complaint Subject</label>
                    <input type="text" id="subject" name="subject"
                           placeholder="Enter complaint subject" maxlength="150" required>
                </div>

                <div class="form-group">
                    <label for="priority">Priority</label>
                    <select id="priority" name="priority" required>
                        <option value="">Select Priority</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Complaint Description</label>
                    <textarea id="description" name="description" rows="6"
                              placeholder="Describe your complaint clearly..." required></textarea>
                </div>

                <button type="submit" class="primary-button form-button">Submit Complaint</button>
            </form>

            <p class="form-footer">
                <a href="dashboard.php">&larr; Back to Dashboard</a>
            </p>

        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>