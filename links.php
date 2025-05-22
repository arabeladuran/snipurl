<?php
session_start();
$mysqli = require __DIR__ . "/database.php";
require "vendor/autoload.php";

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;


if (!isset($_SESSION["user_id"])) {
    header("Location:signup.php");
    exit;
}

$user_id = $_SESSION["user_id"];

$query = "SELECT * FROM links WHERE user_id = ? ORDER BY created_at DESC";

$stmt = $mysqli->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// get results
$result = $stmt->get_result();
$links = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Link History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/links.css" rel="stylesheet">
</head>

<body>
    <?php include "dashboard-nav.php" ?>

    <main>
        <div class="container">
            <div class="card mx-auto p-3 border-0" style="max-width: 800px;">
                <div class="row">
                    <h1>Your links</h1>
                </div>
                <div class="row">
                    <p class="lbl-subtxt">View and edit your links here</p>
                </div>
                <form>
                    <div class="row mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <input type="text" class="inp-search form-control w-75 me-2" placeholder="Search">
                            <a href="dashboard.php" class="btn-create" class="btn btn-primary">Create Link</a>
                        </div>
                    </div>
                </form>
            </div>

            <div class="container">

                <?php if (count($links) === 0): ?>
                    <div class="card card-frame mx-auto mt-3 mb-4 p-3" style="max-width: 800px;">
                        <h2>No links yet</h2>
                    </div>
                <?php else: ?>
                    <?php foreach ($links as $link): ?>
                        <div class="card card-frame mx-auto mt-3 mb-4 p-3" style="max-width: 800px;">
                            <div class="row align-items-center">
                                <div class="col-md-3 text-center mb-3">
                                    <?php if ((int)$link['has_qr'] === 1): ?>
                                        <?php
                                        $writer = new PngWriter();
                                        $qrCode = new QrCode(
                                            data: $link['long_url'],
                                            size: 150,
                                            margin: 10,
                                            foregroundColor: new Color(0, 0, 0),
                                            backgroundColor: new Color(255, 255, 255)
                                        );
                                        $result = $writer->write($qrCode);
                                        $qrImage = base64_encode($result->getString());
                                        ?>
                                        <img src="data:image/png;base64,<?= $qrImage ?>" alt="QR Code">

                                    <?php endif; ?>
                                </div>

                                <div class="col-md-6 " id="title">
                                    <div class="row mb-2">
                                        <div class="col d-flex align-items-center gap-2">
                                            <h2 class="card-title mb-0 me-2" style="font-weight: 800"><?= htmlspecialchars($link['title'] ?? 'Untitled') ?></h2>

                                            <form action="edit-link.php" method="post">
                                                <input type="hidden" name="short_url" value="<?= htmlspecialchars($link['short_url']) ?>">

                                                <button class="btn-edit" class="btn btn-sm btn-primary" id="form-btn">Edit</button>


                                            </form>
                                            <form action="delete.php" method="post" onsubmit="return confirm('Are you sure you want to delete this link?');">
                                                <input type="hidden" name="short_url" value="<?= htmlspecialchars($link['short_url']) ?>">

                                                <button class="btn-del" class="btn btn-sm btn-danger">Delete</button>

                                            </form>
                                        </div>
                                    </div>

                                    <div class="mb-1">
                                        <a href="<?= htmlspecialchars('http://localhost/SnipURL/' . $link['short_url']) ?>" target="_blank">
                                            <p class="short">
                                                localhost/SnipURL/<?= htmlspecialchars($link['short_url']) ?> </p>
                                        </a>
                                        <div>
                                            <p><?= htmlspecialchars($link['long_url']) ?></p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3 d-flex flex-column justify-content-between align-items-end text-end">
                                    <button class="btn-copy" class="btn btn-outline-secondary btn-sm mb-2">Copy</button>
                                    <p class="text-muted mb-0" style="font-size: 0.85rem;">Created on <?= htmlspecialchars($link['created_at']) ?></p>
                                </div>
                            </div>
                        </div>

                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>