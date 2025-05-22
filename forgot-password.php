<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

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
            echo "Message could not be sent. Mailer error: {$mail->ErrorInfo}";
        }
    } else {
        $invalid_email = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link href="styles/global.css" rel="stylesheet">
</head>

<body>
    <!-- to be edited / removed-->
    <nav>
        <a href="index.php" class="nav-logo">SHORTURL</a>
    </nav>

    <main>
        <div class="container" id="form-cnt">
            <h1> Forgot Password </h1>

            <form action="forgot-password.php" method="post">
                <div>
                    <label for="email">Please enter your email</label>
                    <input type="email" id="email" name="email">
                </div>

                <button class="btn" id="form-btn">Send</button>

                <?php if (isset($email_sent)): ?>
                    <span class="invalid">If we find a matching account, we'll send you an email with password recovery instructions. Didn't receive an email? Check your spam folder or try another email address.</span>
                <?php endif; ?>
            </form>
        </div>

    </main>
</body>

</html>