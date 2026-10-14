<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pageTitle = $pageTitle ?? "CampusCare - College Complaint Management System";
$basePath  = $basePath ?? "";
$isAdmin   = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>css/style.css">
</head>
<body>

<header class="site-header">
    <div class="container header-content">

        <a href="<?php echo $basePath; ?>index.php" class="brand">
            <div class="brand-icon">CC</div>
            <div>
                <h1>CampusCare</h1>
                <p>College Complaint Management System</p>
            </div>
        </a>

        <nav class="main-navigation">
            <?php if (isset($_SESSION['user_id'])): ?>

                <?php if ($isAdmin): ?>
                    <a href="<?php echo $basePath; ?>admin/dashboard.php">Dashboard</a>
                    <a href="<?php echo $basePath; ?>admin/complaints.php">Complaints</a>
                <?php else: ?>
                    <a href="<?php echo $basePath; ?>dashboard.php">Dashboard</a>
                    <a href="<?php echo $basePath; ?>complaint.php">Submit Complaint</a>
                    <a href="<?php echo $basePath; ?>view_complaint.php">My Complaints</a>
                <?php endif; ?>

                <a href="<?php echo $basePath; ?>logout.php" class="header-login">Logout</a>

            <?php else: ?>
                <a href="<?php echo $basePath; ?>register.php">Register</a>
                <a href="<?php echo $basePath; ?>login.php" class="header-login">Login</a>
            <?php endif; ?>
        </nav>

    </div>
</header>

<main>