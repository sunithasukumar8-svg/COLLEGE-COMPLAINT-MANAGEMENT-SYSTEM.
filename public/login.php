<?php
session_start();
require_once "config/database.php";

if (isset($_SESSION["user_id"])) {
    header("Location: " . (($_SESSION["role"] ?? "") === "admin" ? "admin/dashboard.php" : "dashboard.php"));
    exit();
}

$pageTitle   = "Login - CampusCare";
$message     = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($email === "" || $password === "") {
        $message = "Please enter your email and password.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        try {
            $stmt = $conn->prepare(
                "SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1"
            );
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user["password"])) {

                session_regenerate_id(true);

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["name"]    = $user["name"];
                $_SESSION["email"]   = $user["email"];
                $_SESSION["role"]    = $user["role"];

                header("Location: " . ($user["role"] === "admin" ? "admin/dashboard.php" : "dashboard.php"));
                exit();
            }

            $message = "Invalid email or password.";
            $messageType = "error";

        } catch (PDOException $e) {
            error_log($e->getMessage());
            $message = "Unable to login. Please try again.";
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
                <span>CampusCare Login</span>
                <h2>Welcome Back</h2>
                <p>Login to access your CampusCare account.</p>
            </div>

            <?php if ($message !== ""): ?>
                <div class="form-message <?php echo htmlspecialchars($messageType); ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" novalidate>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           placeholder="Enter your email address"
                           value="<?php echo htmlspecialchars($_POST["email"] ?? ""); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password"
                           placeholder="Enter your password" required>
                </div>

                <button type="submit" class="primary-button form-button">Login</button>
            </form>

            <p class="form-footer">
                Don't have an account? <a href="register.php">Register here</a>
            </p>

        </div>
    </div>
</section>

<?php include "includes/footer.php"; ?>