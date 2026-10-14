<?php
session_start();
require_once "config/database.php";

$pageTitle   = "Register - CampusCare";
$message     = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name            = trim($_POST["name"] ?? "");
    $email           = trim($_POST["email"] ?? "");
    $password        = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if ($name === "" || $email === "" || $password === "" || $confirmPassword === "") {
        $message = "Please fill in all fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } elseif (strlen($password) < 6) {
        $message = "Password must be at least 6 characters.";
        $messageType = "error";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
        $messageType = "error";
    } else {
        try {
            $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
            $check->execute([$email]);

            if ($check->fetch()) {
                $message = "Email address is already registered.";
                $messageType = "error";
            } else {
                $stmt = $conn->prepare(
                    "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')"
                );
                $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT)]);

                $message = "Registration successful. You can now login.";
                $messageType = "success";
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Registration failed. Please try again.";
            $messageType = "error";
        }
    }
}

include "includes/header.php";
?>

<section class="form-section">
    <div class="container">
        <div class="form-card">

            <div class="section-heading">
                <span>Student Registration</span>
                <h2>Create Your Account</h2>
                <p>Register to submit and track your college complaints.</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                    <?php if ($messageType === "success"): ?>
                        <a href="login.php"><strong>Login now &rarr;</strong></a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" novalidate>
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your full name"
                           value="<?php echo htmlspecialchars($_POST["name"] ?? ""); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email address"
                           value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Minimum 6 characters" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Re-enter your password" required>
                </div>

                <button type="submit" class="primary-button form-button">Create Account</button>
            </form>

            <p class="form-footer">
                Already have an account? <a href="login.php">Login here</a>
            </p>

        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>