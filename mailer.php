<?php

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    // load class files
    require __DIR__ . "/vendor/autoload.php";
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();


    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = $_ENV["MAIL_HOST"];
    $mail->SMTPSecure = PHPMAILER::ENCRYPTION_STARTTLS;
    $mail->Port = $_ENV["MAIL_PORT"];
    $mail->Username = $_ENV["MAIL_USER"];
    $mail->Password = $_ENV["MAIL_PASS"];

    $mail->isHTML(true);

    return $mail;

?>