<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SnipURL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link href="styles/blobs.css" rel="stylesheet">
  <link href="styles/landing.css" rel="stylesheet">
</head>

<body>
  <div class="blob blob1"></div>
  <div class="blob blob2"></div>
  <div class="blob blob3"></div>

  <nav class="navbar">
    <a href="index.php" class="nav-logo">
      <img src="assets/logo.png" alt="SnipURL Logo">
    </a>
    <div class="nav-right">
      <a href="login.php" class="btn">Login</a>
      <a href="signup.php" class="btn" >Start for Free</a>
    </div>
  </nav>

  <div class="container">
    <div class="card">
      <h1>LET’S KEEP THINGS SHORT</h1>
      <input type="url" style="border: 1px solid gray;" placeholder="PASTE YOUR LINK HERE" />
      <br />
      <a href="signup.php" class="btn" id="btn-snip">SNIP</a>
    </div>
  </div>
</body>

</html>