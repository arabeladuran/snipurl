<?php
// form checking
$token = $_POST["token"] ?? $_GET["token"] ?? null;

if (!$token) {
    die("Invalid or missing token.");
}

// check if token is valid
$token_hash = hash("sha256", $token);

require __DIR__ . "/database.php";

$query = "SELECT * FROM users
          WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("s", $token_hash);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user === null) {
    die("token not found");
}

// check if token is still valid
if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("token has expired");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $token = $_POST["token"];
    $errors = [];
    $password = $_POST["password"];

    // validates password, at least 8 characters wt one letter / number
    if (
        strlen($password) < 8
        or !preg_match("/[a-z]/i", $password)
        or ! preg_match("/[0-9]/", $password)
    ) {
        $errors["pw"] = "Password must be at least 8 characters and contain a letter and a number";
    }

    // validates password confimation
    if ($password !== $_POST["confirm-password"]) {
        $errors["pw-confirm"] = "Password does not match";
    }

    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "UPDATE users
                  SET password_hash = ?,
                      reset_token_hash = NULL,
                      reset_token_expires_at = NULL
                  WHERE id = ?";

        $stmt = $mysqli->prepare($query);
        $stmt->bind_param("ss", $password_hash, $user["id"]);
        $stmt->execute();

        // Send confirmation email
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@gmail.com", "SnipURL");
        $mail->addAddress($user["email"]); // assuming you selected the full user row
        $mail->Subject = "Your Password Was Changed";
        $mail->isHTML(true);

        $mail->Body = <<<HTML
        <h2>Password Changed Successfully</h2>
        <p>Hello,</p>
        <p>This is a confirmation that your password has been changed successfully.</p>
        <p>If you did not perform this action, please contact our support team immediately.</p>
        <p>Thanks,<br>The SnipURL Team</p>
    HTML;

        $mail->AltBody = "Hello,\n\nYour password has been successfully changed.\n\nIf you did not perform this, please contact support.\n\nThanks,\nThe SnipURL Team";

        $mail->send();

        session_start();
        $_SESSION["just_reset"] = true;

        header("Location: reset-success.php"); // Redirects after 3 seconds
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles/blobs.css">
    <link href="styles/forgot-password.css" rel="stylesheet">
</head>

<body>
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
    <!-- to be edited / removed-->
    <nav class="container d-flex justify-content-between align-items-center px-3 pt-4 mb-5" style="max-width: 1300px;">
        <a href="index.php" class="nav-logo"><img src="assets/logo.png" alt="SnipURL Logo" style="height: 40px;"></a>
    </nav>

    <main>
        <div class="container">
            <div class="card card-body mx-auto d-flex justify-content-between align-items-center" style="max-width: 500px;">
                <h1 class="card-title my-3"> Reset Password </h1>

                <?php if (isset($errors["pw"])): ?>
                    <div class="alert alert-danger"><?= $errors["pw"] ?></div>
                <?php elseif (isset($errors["pw-confirm"])): ?>
                    <div class="alert alert-danger"><?= $errors["pw-confirm"] ?></div>
                <?php endif; ?>
                <form action="reset-password.php" method="post">
                    <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                    <div class="my-3">
                        <label class="form-label" for="password">New Password</label>
                        <input type="password" id="password" name="password" class="form-control mb-3" style="width:300px; border: 1px solid gray;">
                    </div>

                    <div class="mb-3">
                        <label for="confirm-password" class="form-label">Confirm Password</label>
                        <input type="password" id="confirm-password" name="confirm-password" class="form-control mb-3" style="width:300px; border: 1px solid gray;">
                    </div>

                    <div class="div pt-2">
                        <button class="btn-send">Change Password</button>
                    </div>
                </form>
                <div class="d-flex justify-content-center mb-2">
                    <p>Already have an account? <a href="login.php" style="color: #977dff">Login</a></p>
                </div>
            </div>

        </div>

    </main>
</body>

</html>