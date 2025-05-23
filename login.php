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
    <link rel="stylesheet" href="styles/blobs.css">
    <link href="styles/login.css" rel="stylesheet">
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
        <div class="container mt-3 p-3" style="max-width: 500px;">
                <div class="card border-0" style="min-height: 400px;">
                    <div class="card-body d-flex flex-column justify-content-center align-items-center">
                        <h1 class="card-title my-4" class="mb-4">Login</h1>
                        <form action="login.php" method="post" class="w-100" style="max-width: 300px;">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" style="width:300px; border: 1px solid gray;"
                                    value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" style="width:300px; border: 1px solid gray;">
                                </div>

                                <p class="text-center"><a href="forgot-password.php" style="color: #977dff">Forgot Password?</a></p>

                                <?php if ($is_invalid): ?>
                                    <div class="alert alert-danger">
                                        Invalid email or password.
                                    </div>
                                <?php endif; ?>

                                <div class="d-grid">
                                    <button class="btn-login" class="btn btn-primary">LOGIN</button>
                                </div>
                    </form>

                    <div class="d-flex justify-content-center mt-4">
                        <p> Don't have an account yet? <a href="signup.php" style="color: #977dff" >Sign up</a></p>
                    </div>
            </div>
        </div>

    </main>
</body>

</html>