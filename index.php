<?php
    session_start();
    if(isset($_SESSION["user_id"])) {
        header("Location: dashboard.php");
        exit;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Snip URL</title>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka+One&display=swap" rel="stylesheet" />
  <style>
    body {
      margin: 0;
      font-family: 'Fredoka One', cursive;
      overflow: hidden;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      color: #2c1e4a;
    }

    /*MESH BG KULAYS AND ANIMATION*/

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
      0%, 100% {
        transform: translate(0%, 0%) scale(1);
        filter: blur(80px) brightness(1);
      }
      50% {
        transform: translate(12%, 12%) scale(1.15);
        filter: blur(85px) brightness(1.1);
      }
    }

    /*KULAY NG MGA BLOB AND ANIMATION*/

    .blob {
      position: absolute;
      width: 400px;
      height: 400px;
      border-radius: 50%;
      filter: blur(100px);
      opacity: 0.7;
      animation: moveBlobs 15s cubic-bezier(0.4, 0, 0.2, 1) infinite;
      animation-direction: alternate;
      z-index: -1;
    }

    .blob1 {
      background: #e2d4f7;
      top: 10%;
      left: 10%;
      animation-delay: 0s;
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
      0% {
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
      100% {
        transform: scale(1) translate(0, 0) rotate(0deg);
        opacity: 0.7;
      }
    }

    /*PRA SA PINAKA LANDING PAGE*/
    .logo {
      position: fixed;
      top: 20px;
      left: 30px;
      font-size: 1.4rem;
      color: #2c1e4a;
      z-index: 999;
    }

    .container {
      text-align: center;
      position: relative;
      padding: 2rem;
      box-sizing: border-box;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100%;
      width: 100%;
    }


    /*D2 NAKALAGAY MGA MAKIKITA MO PRA SA PASTE LINK*/
    .card {
      background: white;
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1);
      width: 90vw;
      max-width: 500px;
      margin: 2rem auto
    }

    .card h1 {
      font-size: 2.5rem;
      margin-bottom: 20px;
    }

    input[type="url"] {
      width: 80%;
      padding: 15px;
      border: none;
      border-radius: 12px;
      background: #dfe6f0;
      color: #2c1e4a;
      font-size: 1rem;
      margin-bottom: 20px;
      text-align: center;
    }

    input::placeholder {
      color: #8c84c4;
    }

    .btn {
      background: #2c1e4a;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 20px;
      font-size: 1rem;
      cursor: pointer;
      transition: background 0.3s;
      font-family: 'Fredoka One', cursive;
    }

    .btn:hover {
      background: #49366d;
    }

    /*KITA MO NMN SA NAME ETO YUNG MGA NASA TAAS*/
    .top-buttons {
      position: fixed;
      top: 20px;
      right: 30px;
      z-index: 999;
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    .top-buttons button {
      background: #2c1e4a;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 20px;
      font-size: 0.9rem;
      cursor: pointer;
      transition: background 0.3s;
      font-family: 'Fredoka One', cursive;
    }

    .top-buttons button:hover {
      background: #49366d;
    }

    /* ✅ RESPONSIVE MEDIA QUERIES */
    @media (max-width: 600px) {
      .card h1 {
        font-size: 1.8rem;
      }

      .logo {
        font-size: 1.2rem;
        left: 15px;
      }

      .top-buttons {
        top: 10px;
        right: 15px;
        flex-direction: column;
      }

      .top-buttons button {
        font-size: 0.8rem;
        padding: 8px 14px;
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

  <div class="logo">SNIP-URL</div>
  <div class="top-buttons">
    <button id="loginBut">LOGIN</button>
    <button id="signupBut">START FOR FREE</button>
  </div>
  <div class="container">
    <div class="card">
      <h1>LET’S KEEP THINGS SHORT</h1>
      <input type="url" placeholder="PASTE YOUR LINK HERE" />
      <br />
      <button class="btn" id="snipBut">SNIP URL</button>
    </div>
  </div>
  <script>
    document.getElementById("snipBut").addEventListener("click", function () {
      window.location.href = "login.php";
    });
    document.getElementById("loginBut").addEventListener("click", function () {
      window.location.href = "login.php";
    });
    document.getElementById("signupBut").addEventListener("click", function () {
      window.location.href = "signup.php";
    });
  </script>

</body>
</html>
