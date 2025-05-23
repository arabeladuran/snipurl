<?php
session_start();

if(!isset($_SESSION["just_reset"])) {
    header("Location: index.php");
    exit;
}

unset($_SESSION["just_reset"]);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SnipURL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="styles/blobs.css">
    <link href="styles/signup.css" rel="stylesheet">
</head>

<body>
    <div class="blob blob1"></div>
    <div class="blob blob2"></div>
    <div class="blob blob3"></div>
    <!-- to be edited / removed-->
    <div class="container" style="max-width: 1200px;">
        <nav class="navbar p-3">
            <a href="index.php" class="nav-logo">SHORTURL</a>
        </nav>
    </div>

    <main>
        <div class="container" style="margin-top: 100px;" id="form-cnt">
            <div class="card border-0">
                <div class="card-body d-flex flex-column justify-content-center align-items-center" style="width: 800px;">
                    <h1 class="card-title mb-5" style="font-size: 40px"> Your password has been changed </h1>
                    <a class="btn-signup" class="btn btn-primary my-5"  href="login.php" style="text-decoration: none;">Continue to Login</a>
                </div>
            </div>
        </div>
    </main>
</body>

</html>