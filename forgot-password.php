<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    if (empty($email)) {
        $invalid_email = true;
    } else {
        $invalid_email = false;
    }

    if (!$invalid_email) {
        // generate random token and create hash value
        $token = bin2hex(random_bytes(16));
        $token_hash = hash("sha256", $token);

        // token only valid for 15 mins
        $expiry = date("Y-m-d H:i:s", time() + 60 * 15);

        $mysqli = require __DIR__ . "/database.php";

        $query = "UPDATE users
              SET reset_token_hash = ?,
              reset_token_expires_at = ?
              WHERE email = ?";

        $stmt = $mysqli->prepare($query);

        $stmt->bind_param("sss", $token_hash, $expiry, $email);

        $stmt->execute();

        $email_sent = true;
        if ($mysqli->affected_rows) {
            $mail = require __DIR__ . "/mailer.php";

            $mail->setFrom("noreply@gmail.com");
            $mail->addAddress($email);
            $mail->Subject = "Password Reset";

            $mail->Body = <<<HTML
        <h2>Password Reset Request</h2>
        <p>Hello,</p>
        <p>You requested a password reset. Click the button below to reset it:</p>
        <p>
            <a href="http://localhost/SnipURL/reset-password.php?token=$token" style="display:inline-block;padding:10px 20px;background-color:#4CAF50;color:#fff;text-decoration:none;border-radius:5px;">Reset Password</a>
        </p>
        <p>If you did not request this, please ignore this email.</p>
        <p>Thanks,<br>The SnipURL Team</p>
    HTML;

            try {
                $mail->send();
            } catch (Exception $e) {
                $error = "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
            }
        } else {
            $invalid_email = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
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
                <h1 class="card-title mt-3"> Forgot Password </h1>

                <?php if (isset($email_sent)): ?>
                    <div class="alert alert-success mt-3">If we find a matching account, we'll send you an email with password recovery instructions. Didn't receive an email? Check your spam folder or try another email address.</div>
                <?php elseif (isset($invalid_email)): ?>
                    <div class="alert alert-danger mt-3">
                        Please enter your email address.
                    </div>
                <?php endif; ?>

                <form action="forgot-password.php" method="post">
                    <div class="mb-3">
                        <label for="email" class="form-label pt-3">Email</label>
                        <input type="email" id="email" name="email" class="form-control mb-3" style="width:300px; border: 1px solid gray;">
                    </div>

                    <div class="d-grid pt-2">
                        <button class="btn-send" class="btn btn-primary">Reset</button>
                </form>
            </div>
        </div>
    </main>
</body>

</html>