<?php 
    require __DIR__ . "/vendor/autoload.php";

    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();

    $mysqli = new mysqli($_ENV["DB_HOST"],
                         $_ENV["DB_USER"],
                         $_ENV["DB_PASS"],
                         $_ENV["DB_NAME"]);

    if ($mysqli->connect_errno) {
        die("Connection error: ". $mysqli->connect_error);
    }

    return $mysqli;
?>