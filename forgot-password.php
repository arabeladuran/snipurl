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

    if ($mysqli->affected_rows) {
        $mail = require __DIR__ . "/mailer.php";

        $mail->setFrom("noreply@gmail.com");
        $mail->addAddress($email);
        $mail->Subject = "Password Reset";
        $mail->Body = <<<END

        Click <a href="http://localhost/SnipURL/reset-password.php?token=$token">here</a> to reset your password.
        
        END;

        try {
            $mail->send();
            $email_sent = true;
            
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

        <ul class="nav-links">
            <li class="link"><a href="">Features</a></li>
            <li class="link"><a href="">About</a></li>
        </ul>

        <div class="nav-btn">
            <a href="login.php" class="btn" id="login-btn">Log in</a>
            <a href="signup.php" class="btn" id="signup-btn">Start for Free</a>
        </div>
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
                    <em class="invalid">Email has been sent.</em>
                <?php elseif (isset($invalid_email)): ?>
                    <em class="invalid">Account does not exist.</em>
                <?php endif; ?>
            </form>
        </div>

    </main>
</body>

</html>