<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Snip URL</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: #2c1e4a;
    }

    /* Mesh Background */
    body::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 30% 30%, #f9e3b4, transparent 40%),
        radial-gradient(circle at 70% 40%, #e2d4f7, transparent 40%),
        radial-gradient(circle at 50% 70%, #fcd6e8, transparent 40%),
        radial-gradient(circle at 80% 80%, #cde7f9, transparent 40%);
      animation: meshMove 20s ease-in-out infinite;
      z-index: -1;
      filter: blur(80px);
    }

    @keyframes meshMove {

      0%,
      100% {
        transform: translate(0%, 0%) scale(1);
        filter: blur(80px) brightness(1);
      }

      50% {
        transform: translate(12%, 12%) scale(1.15);
        filter: blur(85px) brightness(1.1);
      }
    }

    .blob {
      position: absolute;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      filter: blur(100px);
      opacity: 0.7;
      animation: moveBlobs 15s cubic-bezier(0.4, 0, 0.2, 1) infinite alternate;
      z-index: -1;
    }

    .blob1 {
      background: #e2d4f7;
      top: 10%;
      left: 10%;
    }

    .blob2 {
      background: #f9e3b4;
      top: 50%;
      left: 60%;
      animation-delay: 5s;
    }

    .blob3 {
      background: #fcd6e8;
      top: 30%;
      left: 40%;
      animation-delay: 10s;
    }

    @keyframes moveBlobs {

      0%,
      100% {
        transform: scale(1) translate(0, 0) rotate(0deg);
        opacity: 0.7;
      }

      25% {
        transform: scale(1.3) translate(80px, -40px) rotate(15deg);
        opacity: 0.85;
      }

      50% {
        transform: scale(1.5) translate(100px, -80px) rotate(0deg);
        opacity: 0.7;
      }

      75% {
        transform: scale(1.3) translate(80px, -40px) rotate(-15deg);
        opacity: 0.85;
      }
    }

    .container {
      text-align: center;
      position: relative;
      padding: 2rem;
      display: flex;
      justify-content: center;
      align-items: center;
      width: 100%;
      height: 100%;
      box-sizing: border-box;
    }

    .card {
      background: white;
      border-radius: 20px;
      padding: 50px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      width: 95vw;
      max-width: 600px;
    }


    .card h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      font-weight: 900;
    }


    input[type="url"] {
      font-family: 'Poppins', sans-serif;
      max-width: 400px;
      width: 100%;
      padding: 10px;
      color: #2c1e4a;
      border-radius: 5px;
      border: 2px solid transparent;
      background-color: #dfe6f0;
      font-size: 1rem;
      text-align: center;
      transition: all 0.3s ease;
      margin-bottom: 24px;
    }

    input[type="url"]:focus {
      outline: none;
      background-image: linear-gradient(120deg, white, #cdb4db);
      /* fallback for var(--purp) */
      border-image: linear-gradient(120deg, #2c1e4a, #a08bd0);
      /* fallback for var(--black), var(--purp2) */
      border-image-slice: 1;
      box-shadow: 0 0px 5px #a08bd0;
    }


    input::placeholder {
      color: #8c84c4;
    }

    a {
      text-decoration: none;
    }

    .btn {
      background: #2c1e4a;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 10px;
      font-size: 1rem;
      cursor: pointer;
      transition: transform 0.2s ease;
    }

    .btn:hover {
      background: #49366d;
      transform: scale(1.1);
    }

    #btn-snip {
      padding: 8px 30px;
      color: white;
      font-weight: 800;
      border: none;
      border-radius: 10px;
      box-shadow: 0 1px 3px grey;
      background-image: linear-gradient(130deg, #1b283a, #3113a8);

      transition: ease 0.2s;
    }

    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 24px 40px;
      font-family: 'Poppins', sans-serif;
      max-width: 1283px;
      margin: auto;
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 999;
    }

    .nav-logo img {
      height: 40px;
      transition: transform 0.3s ease;
    }

    .nav-logo:hover img {
      transform: scale(1.1);
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .navbar .btn {
      padding: 10px 20px;
      border-radius: 12px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }


    @media (max-width: 600px) {
      .card h1 {
        font-size: 1.8rem;
      }

      .blob {
        width: 250px;
        height: 250px;
        filter: blur(80px);
      }
    }
  </style>
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
      <a href="signup.php" class="btn">Start for Free</a>
    </div>
  </nav>

  <div class="container">
    <div class="card">
      <h1>LET’S KEEP THINGS SHORT</h1>
      <input type="url" style="border: 1px solid gray;" placeholder="PASTE YOUR LINK HERE" />
      <br />
      <button class="btn" id="btn-snip">SNIP URL</button>
    </div>
  </div>
</body>

</html>