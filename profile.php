<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: signup.php");
    exit;
}

$mysqli = require __DIR__ . "/database.php";

$user_id = $_SESSION["user_id"];
$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm-password"];

    if (empty($name)) {
        $errors["name"] = "This field is required";
    }

    if (empty($email)) {
        $errors["email"] = "This field is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors["email"] = "Please enter a valid email address";
    }

    // Check if new email is taken by another user
    if (!array_key_exists("email", $errors)) {
        $stmt = $mysqli->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors["email-taken"] = "Email is already taken";
        }
    }

    // Validate password (only if provided)
    if (!empty($password)) {
        if (
            strlen($password) < 8 ||
            !preg_match("/[a-z]/i", $password) ||
            !preg_match("/[0-9]/", $password)
        ) {
            $errors["pw"] = "Password must be at least 8 characters and contain a letter and a number";
        }

        if ($password !== $confirm_password) {
            $errors["pw-confirm"] = "Password does not match";
        }

        // If valid, update the database
        if (empty($errors)) {
            if (!empty($password)) {
                $password_hash = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ?, password_hash = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $email, $password_hash, $user_id);
            } else {
                $stmt = $mysqli->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
                $stmt->bind_param("ssi", $name, $email, $user_id);
            }

            if ($stmt->execute()) {
                $success = true;
            } else {
                $errors["db"] = "Database error: " . $stmt->error;
            }
        }
    }
}

$query = "SELECT * FROM users WHERE id = ? ";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/profile.css" rel="stylesheet">
    <link href="styles/nav.css" rel="stylesheet">
</head>

<body>
    <?php include "dashboard-nav.php" ?>

    <main>
        <div class="container d-flex justify-content-center align-items-center mt-5" style="max-width: 550px;">
            <div class="card card-body mx-auto">
                <h1 class="card-title">
                    Edit Profile
                </h1>

                <?php if (isset($email_sent)): ?>
                    <div class="alert alert-success mt-3">Changes has been saved.</div>
                <?php endif; ?>

                <form action="profile.php" method="post">
                    <div class="mb-2">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" name="name" id="name" class="form-control"
                            value="<?= htmlspecialchars($_POST["name"] ?? $user['name']) ?>">

                        <?php if (isset($errors["name"])): ?>
                            <em class="text-danger"><?= $errors["name"] ?></em>
                        <?php endif; ?>
                    </div>

                    <div class="mb-2">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="<?= htmlspecialchars($_POST["email"] ?? $user['email']) ?>">

                        <?php if (isset($errors["email"])): ?>
                            <em class="text-danger"><?= $errors["email"] ?></em>
                        <?php elseif (isset($errors["email-taken"])): ?>
                            <em class="text-danger"><?= $errors["email-taken"] ?></em>
                        <?php endif; ?>
                    </div>

                    <h5 class="my-3">Change Password</h5>
                    <div class="mb-3">
                        <input type="password" name="password" id="password" class="form-control" placeholder="New password">
                    </div>
                    <div class="mb-3">
                        <input type="password" name="confirm-password" id="confirmpassword" class="form-control mb-4" placeholder="Confirm password">

                        <?php if (isset($errors["pw"])): ?>
                            <div class="alert alert-danger"><?= $errors["pw"] ?></div>
                        <?php elseif (isset($errors["pw-confirm"])): ?>
                            <div class="alert alert-danger"><?= $errors["pw-confirm"] ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <button class="btn-cancel" class="btn btn-secondary" type="button" onclick="history.back()">Back</button>
                        <button class="btn-save" class="btn btn-primary" type="submit">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
        </div>
    </main>
</body>

</html>