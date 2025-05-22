<?php
session_start();
$mysqli = require __DIR__ . "/database.php";
require "vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;

// prevent access to dashboard when not logged in
if (!isset($_SESSION["user_id"])) {
    header("Location:signup.php");
    exit;
}

// generate random link
function generateRandomString($length = 6): string
{
    $chars = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    $charlength = strlen($chars);
    $randomString = '';

    for ($i = 0; $i < $length; $i++) {
        $randomString .= $chars[random_int(0, $charlength - 1)];
    }

    return $randomString;
}

function isCodeUnique($mysqli, $short_url): bool
{
    $query = "SELECT id FROM links WHERE short_url = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $short_url);
    $stmt->execute();
    return $stmt->get_result()->num_rows === 0;
}

function isLinkValid(&$long_url)
{
    if (!preg_match("~^(http|https)://~", $long_url)) {
        $long_url = "https://" . $long_url;
    }

    if (! filter_var($long_url, FILTER_VALIDATE_URL)) {
        return false;
    }

    $url_parts = parse_url($long_url);
    if (!isset($url_parts['host'])) {
        return false;
    }

    // host must contain at least one dot (e.g., example.com)
    if (substr_count($url_parts['host'], '.') < 1) {
        return false;
    }

    // heck that domain and TLD have minimum length, e.g. domain >= 2 chars
    $host_parts = explode('.', $url_parts['host']);
    foreach ($host_parts as $part) {
        if (strlen($part) < 2) {
            return false;
        }
    }

    return true;
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $long_url = trim($_POST["long-url"] ?? "");
    $short_url = trim($_POST["short-url"] ?? "");
    $title = trim($_POST["title"] ?? "");
    $user_id = $_SESSION["user_id"];
    $has_qr = 1;

    $errors = [];

    // form validation
    if (empty($long_url)) {
        $errors["long_url"] =  "Enter a link";
    } elseif (! isLinkValid($long_url)) {
        $errors["long_url"] = "Please enter a valid link";
    }

    if (empty($title)) {
        $title = "Untitled";
    }

    if (empty($errors)) {
        // if no custom code provided
        if (empty($short_url)) {
            do {
                $short_url = generateRandomString();
            } while (!isCodeUnique($mysqli, $short_url));
        } else {
            if (!isCodeUnique($mysqli, $short_url)) {
                $errors["short_url"] = "Link is already taken";
            }
        }

        if (empty($errors)) {
            $created_at = date("Y-m-d H:i:s", time());

            $query = "INSERT INTO links (user_id, title, long_url, short_url, has_qr, created_at) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("isssis", $user_id, $title, $long_url, $short_url, $has_qr, $created_at);
            $stmt->execute();

            if ($has_qr) {
                $writer = new PngWriter();

                // Create QR code
                $qrCode = new QrCode(
                    data: $long_url,
                    size: 200,
                    margin: 10,
                    foregroundColor: new Color(0, 0, 0),
                    backgroundColor: new Color(255, 255, 255)
                );

                // Write the result
                $result = $writer->write($qrCode);

                // Convert to base64
                $qrImage = base64_encode($result->getString());
            }

            $success = "Link created!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/dashboard.css" rel="stylesheet">
    <link href="styles/nav.css" rel="stylesheet">

</head>

<body>

    <?php include "dashboard-nav.php" ?>

    <main>
        <div class="container p-5" style="max-width: 700px;">
            <div class="card border-0 p-3">
                <div class="card-body">
                    <h1 class="card-title mb-3"> Snip your Links in a Snap. </h1>
                    <form method="post">
                        <div class="mb-4">
                            <label class="form-label" for="long-url">Enter long URL</label>
                            <input type="text" id="long-url" name="long-url" placeholder="e.g. https://example.com/longlink" class="form-control"
                                value="<?= htmlspecialchars($_POST["long_url"] ?? "") ?>">


                            <?php if (isset($errors["long_url"])): ?>
                                <em class="invalid"><?= $errors["long_url"] ?></em>
                            <?php endif; ?><form method="post">

                        </div>
                        <div class="mb-4">
                            <label class="form-label" for="title">Title (Optional)</label>
                            <input type="text" id="title" name="title" class="form-control"
                                value="<?= htmlspecialchars($_POST["title"] ?? "") ?>">
                        </div>
                        <div class="row mb-4">
                            <label for="short-url" class="form-label">Custom URL</label>
                            <div class="col">
                                <input type="text" id="default-url" value="www.snip-url.com/" class="form-control" readonly>
                            </div>
                            <div class="col">
                                <input type="text" id="short-url" name="short-url" class="form-control"
                                    value="<?= htmlspecialchars($_POST["short_url"] ?? "") ?>">
                            </div>

                            <?php if (isset($errors["short_url"])): ?>
                                <em class="invalid"><?= $errors["short_url"] ?></em>
                            <?php endif; ?>
                        </div>

                        <button class="btn-snip" id="form-btn">Snip your link</button>
                </div>

                </form>
            </div>
        </div>
        </div>
    </main>

    <?php if (isset($success)): ?>
        <script>
            window.addEventListener("load", function() {
                const modal = new bootstrap.Modal(document.getElementById('successModal'));
                modal.show();
            });
        </script>
    <?php endif; ?>


    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="width: 400px;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="successModalLabel">Link Created!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <?php if (isset($qrImage)): ?>
                        <p class="form-label">QR Code:</p>
                        <img id="qr-code-img" src="data:image/png;base64,<?= $qrImage ?>" alt="QR Code" class="img-fluid rounded border mb-3" style="max-width: 250px;">
                    <?php endif; ?>
                    <p class="form-label">Your short URL:</p>
                    <a id="short-url-link" href="http://localhost/SnipURL/<?= $short_url ?>" target="_blank">
                        http://localhost/SnipURL/<?= $short_url ?>
                    </a>
                </div>
                <div class="modal-footer">
                    <button class="btn-snip" onclick="copyToClipboard()">Copy</button>
                    <button class="btn-snip" onclick="downloadQR()">Download QR</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard() {
            const link = document.getElementById("short-url-link").href;
            navigator.clipboard.writeText(link).then(() => {
                alert("Link copied to clipboard!");
            });
        }

        function downloadQR() {
            const img = document.getElementById("qr-code-img");
            const a = document.createElement("a");
            a.href = img.src;
            a.download = "qr-code.png";
            a.click();
        }
    </script>

</body>

</html>