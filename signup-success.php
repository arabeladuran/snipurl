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
            <h1> Welcome!  </h1>
            <a href="dashboard.php">Continue to Dashboard</a>
        </div>
    </main>
</body>

</html>