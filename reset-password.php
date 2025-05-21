<?php
// form checking
$token = $_GET["token"] ?? null;

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

        echo ("Password has been changed. Redirecting to Login...");

        // send another email stating that the password has been changed.
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/global.css">
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
            <!-- If user is not logged in, redirect to sign up page -->
            <a href="login.php" class="btn" id="login-btn">Log in</a>
            <a href="signup.php" class="btn" id="signup-btn">Start for Free</a>
        </div>
    </nav>

    <main>
        <div class="container" id="form-cnt">
            <h1> Reset Password </h1>

            <form action="" method="post">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

                <div>
                    <label for="password">New Password</label>
                    <input type="password" id="password" name="password">
                </div>

                <div>
                    <label for="confirm-password">Confirm Password</label>
                    <input type="password" id="confirm-password" name="confirm-password">
                </div>

                <?php if (isset($errors["pw"])): ?>
                    <span class="invalid"><?= $errors["pw"] ?></span>
                <?php elseif (isset($errors["pw-confirm"])): ?>
                    <span class="invalid"><?= $errors["pw-confirm"] ?></span>
                <?php endif; ?>

                <button class="btn" id="form-btn">Change Password</button>
            </form>

        </div>

    </main>
</body>

</html>