<?php

$is_invalid = false;

// check if form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // connect to database
    $mysqli = require __DIR__ . "/database.php";

    // check records
    $query = sprintf(
        "SELECT * FROM users WHERE email = '%s'",
        $mysqli->real_escape_string($_POST["email"])
    );

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- to be edited / removed-->
    <nav class="container d-flex justify-content-between align-items-center p-5" style="max-width: 1200px;">
        <a href="index.php" class="nav-logo">SHORTURL</a>
    </nav>

    <main>
        <div class="container mt-3 p-3" style="max-width: 500px;">
            <div class="card">
                <div class="card-body">
                    <h1> Login </h1>

                    <form action="login.php" method="post">
                        <div class="my-3">
                            <input type="email" class="form-control" id="email" name="email"
                                value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                            <!-- ^^ retains email in input box when reloading the form -->
                        </div>

                        <div class="mb-3">
                            <input type="password" class="form-control" id="password" name="password">
                        </div>
                        <p style="text-align:center"> <a href="forgot-password.php">Forgot Password?</a> </p>

                        <!-- displays error when email / password is wrong -->
                        <?php if ($is_invalid): ?>
                            <div class="alert alert-danger">
                                Invalid email or password.
                            </div>
                        <?php endif; ?>

                        <button class="btn btn-primary">Login</button>
                    </form>
                    <div class="d-flex justify-content-center">
                        <p> Don't have an account yet?
                            <a href="signup.php">Sign up</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</body>

</html>