<?php
session_start();
$pageTitle = "CampusCare - College Complaint Management System";
include "includes/header.php";
?>

<section class="hero-section">
    <div class="container hero-content">
        <div class="hero-text">
            <span class="welcome-badge">Student Support Portal</span>
            <h2>Your Voice Matters. <span>We Listen.</span></h2>
            <p>
                CampusCare gives students a simple, transparent way to raise
                college-related complaints, follow their progress, and receive
                written responses from the administration.
            </p>
            <div class="hero-buttons">
                <a href="register.php" class="primary-button">Register</a>
                <a href="login.php" class="secondary-button">Student / Admin Login</a>
            </div>
        </div>

        <div class="hero-card">
            <div class="card-icon">✓</div>
            <h3>Complaint Tracking</h3>
            <p>Follow your complaint from submission to resolution.</p>
            <div class="status-flow">
                <div class="status-item"><span class="status-dot pending"></span><span>Pending</span></div>
                <div class="status-line"></div>
                <div class="status-item"><span class="status-dot progress"></span><span>In Progress</span></div>
                <div class="status-line"></div>
                <div class="status-item"><span class="status-dot resolved"></span><span>Resolved</span></div>
            </div>
        </div>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <div class="section-heading">
            <span>CampusCare Features</span>
            <h2>Simple. Transparent. Student-Friendly.</h2>
            <p>Manage every college complaint through one organized platform.</p>
        </div>

        <div class="feature-grid">
            <div class="feature-card">
                <div class="feature-number">01</div>
                <h3>Submit Complaints</h3>
                <p>Report issues about hostel, transport, canteen, academics and more.</p>
            </div>
            <div class="feature-card">
                <div class="feature-number">02</div>
                <h3>Track Status</h3>
                <p>Every complaint moves through Pending, In Progress and Resolved.</p>
            </div>
            <div class="feature-card">
                <div class="feature-number">03</div>
                <h3>Admin Response</h3>
                <p>Administrators review, update status and reply to each complaint.</p>
            </div>
            <div class="feature-card">
                <div class="feature-number">04</div>
                <h3>Unique Reference ID</h3>
                <p>Every complaint gets its own ID for easy identification.</p>
            </div>
        </div>
    </div>
</section>

<section class="categories-section">
    <div class="container">
        <div class="section-heading">
            <span>Complaint Categories</span>
            <h2>Raise the Right Complaint</h2>
        </div>
        <div class="category-grid">
            <div class="category-item">Hostel</div>
            <div class="category-item">Transport</div>
            <div class="category-item">Canteen</div>
            <div class="category-item">Academics</div>
            <div class="category-item">Infrastructure</div>
            <div class="category-item">Examination</div>
            <div class="category-item">Other</div>
        </div>
    </div>
</section>

<section class="college-section">
    <div class="container college-content">
        <div>
            <span class="college-label">College Portal</span>
            <h2>ABC College of Engineering</h2>
            <p>A dedicated portal for better communication between students and administration.</p>
        </div>
        <a href="register.php" class="primary-button">Get Started</a>
    </div>
</section>

<?php include "includes/footer.php"; ?>