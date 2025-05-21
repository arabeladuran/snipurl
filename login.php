<?php

$is_invalid = false;

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // connect to database
    $mysqli = require __DIR__ . "/database.php";

    // check records
    $query = sprintf("SELECT * FROM users WHERE email = '%s'", 
                    $mysqli->real_escape_string($_POST["email"]));

    $result = $mysqli->query($query);

    // get results
    $user = $result->fetch_assoc();

    // if user exists
    if ($user) {
        // check password
        if (password_verify($_POST["password"], $user["password_hash"])) {
            session_start();

            // regenerate to avoid session fixation attack
            session_regenerate_id();

            $_SESSION["user_id"] = $user["id"];

            // redirect to home page
            header("Location: dashboard.php");
            exit;
        } 
    }

    // if wrong password
    $is_invalid = true;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="styles/global.css" rel="stylesheet">
</head>

<body>
    <!-- to be edited / removed-->
    <nav>
        <a href="index.php" class="nav-logo">SHORTURL</a>
    </nav>

    <main>
        <div class="container" id="form-cnt">
            <h1> Login </h1>

            <form action="login.php" method="post">
                <div>
                    <input type="email" id="email" name="email"
                            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                        <!-- ^^ retains email in input box when reloading the form --> 
                </div>

                <div>
                    <input type="password" id="password" name="password">
                </div>
                <p style="text-align:center"> <a href="forgot-password.php">Forgot Password?</a> </p>

                <!-- displays error when email / password is wrong --> 
                <?php if ($is_invalid): ?>
                    <span class="invalid">Invalid email or password</span>
                <?php endif; ?>
                <button class="btn">Login</button>
            </form>
            <p> Don't have an account yet? 
            <a href="signup.php">Sign up</a> </p>
        </div>

    </main>
</body>

</html>