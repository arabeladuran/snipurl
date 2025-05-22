<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $errors = [];


    // ff code validates the inputs

    if (empty($name)) {
        $errors["name"] = "* This field is required";
    }

    // validates email address
    if (empty($email)) {
        $errors["email"] = "* This field is required";
    } elseif (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "* Please enter a valid email address";
    }

    // validates password, at least 8 characters wt one letter / number
    if (
        strlen($password) < 8
        or !preg_match("/[a-z]/i", $password)
        or ! preg_match("/[0-9]/", $password)
    ) {
        $errors["pw"] = "* Password must be at least 8 characters and contain a letter and a number";
    }

    // validates password confimation
    if ($password !== $_POST["confirm-password"]) {
        $errors["pw-confirm"] = "* Password does not match";
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/signup.css" rel="stylesheet">
</head>

<body>
    <!-- to be edited / removed-->
    <nav class="container d-flex justify-content-between align-items-center p-5" style="max-width: 1200px;">
        <a href="index.php" class="nav-logo">SHORTURL</a>
    </nav>

    <main>
        <<div class="card border-0" style="min-height: 550px;">
                <div class="card-body d-flex flex-column justify-content-center align-items-center">
                    <h1 class="card-title mb-3">Sign Up</h1>
                     <form action="signup.php" method="post" id="signup" class="w-100" style="max-width: 300px;" novalidate>
                        <div class="mb-4">
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                value="<?= htmlspecialchars($name ?? "") ?>">

                            <?php if (isset($errors["name"])): ?>
                                <em class="invalid"><?= $errors["name"] ?></em>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                value="<?= htmlspecialchars($email ?? "") ?>">

                            <?php if (isset($errors["email"])): ?>
                                <em class="invalid"><?= $errors["email"] ?></em>
                            <?php elseif (isset($errors["email-taken"])): ?>
                                <em class="invalid"><?= $errors["email-taken"] ?></em>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter password"> 
                        </div>

                        <div class="mb-4">
                            <input type="password" class="form-control" id="confirm-password" name="confirm-password" placeholder="Confirm password">

                            <?php if (isset($errors["pw"])): ?>
                               <em> <span  class="invalid"><?= $errors["pw"] ?></span> </em>
                            <?php elseif (isset($errors["pw-confirm"])): ?>
                                <em> <span class="invalid"><?= $errors["pw-confirm"] ?></span>  </em> 
                            <?php endif; ?>
                        </div>

                        <div class="d-grid">
                        <button class="btn-signup" class="btn btn-primary mb-3">Sign Up</button>
                            </div>
                    </form>

                    <div class="d-flex justify-content-center mt-3">
                        <p>Already have an account? <a href="login.php" style="color: #977dff">Login</a></p>
                    </div>

                </div>
            </div>
        </div>
    </main>
</body>

</html>