<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $errors = [];


    // ff code validates the inputs

    if (empty($name)) {
        $errors["name"] = "This field is required";
    }

    // validates email address
    if (empty($email)) {
        $errors["email"] = "This field is required";
    } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address";
    }

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

    // check if valid email is already taken
    if (!array_key_exists("email", $errors)) {

        // connect to database
        $mysqli = require __DIR__ . "/database.php";

        // check if email is already taken
        $check_stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $errors["email-taken"] = "Email is already taken";
        }
    }

    // if there are no errors, create the account
    if (empty($errors)) {

        // hash password for security
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)";

        // checks for syntax errors
        $stmt = $mysqli->stmt_init();
        if (! $stmt->prepare($query)) {
            die("SQL Error: " . $mysqli->error);
        }

        $stmt->bind_param("sss", $name, $email, $password_hash);

        try {
            $stmt->execute();

            // start session to immediately log user in after successful sign up
            session_start();
            session_regenerate_id(); // prevent session fixation attack

            $_SESSION["user_id"] = $mysqli->insert_id; // retrieves the most recent inserted id

            //redirects page when signup is successful
            header("Location: signup-success.php");
            exit;
            
        } catch (mysqli_sql_exception $e) {
            throw $e;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SignUp</title>
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
            <?php
            if (isset($user)): ?>
                <a href="logout.php" class="btn" id="signup-btn">Log out</a>
            <?php else: ?>
                <!-- If user is not logged in, redirect to sign up page -->
                <a href="login.php" class="btn" id="login-btn">Log in</a>
                <a href="signup.php" class="btn" id="signup-btn">Start for Free</a>
            <?php endif; ?>

        </div>
    </nav>

    <main>
        <div class="container" id="form-cnt">
            <h1> Signup </h1>

            <form action="signup.php" method="post" id="signup" novalidate>
                <div>
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name"
                        value="<?= htmlspecialchars($name ?? "") ?>">
                </div>
                <?php if (isset($errors["name"])): ?>
                    <em class="invalid"><?= $errors["name"] ?></em>
                <?php endif; ?>

                <div>
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                        value="<?= htmlspecialchars($email ?? "") ?>">
                </div>
                <?php if (isset($errors["email"])): ?>
                    <em class="invalid"><?= $errors["email"] ?></em>
                <?php elseif (isset($errors["email-taken"])): ?>
                    <em class="invalid"><?= $errors["email-taken"] ?></em>
                <?php endif; ?>

                <div>
                    <label for="password">Password</label>
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
                <button class="btn" id="form-btn">Sign Up</button>
            </form>

            <p>Already have an account? <a href="login.php">Login</a></p>
        </div>

    </main>
</body>

</html>