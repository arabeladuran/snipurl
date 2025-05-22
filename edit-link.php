<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

if (!isset($_SESSION["user_id"])) {
    header("Location:signup.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$errors = [];
$success = "";
$link = null;

// Ensure short_url exists
$short_url = $_POST["short_url"] ?? $_GET["short_url"] ?? null;
if (!$short_url) {
    header("Location: links.php");
    exit;
}

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

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "save") {
    $new_long_url = trim($_POST["long_url"]);
    $new_short_url = trim($_POST["new_short_url"]);
    $title = trim($_POST["title"]);
    // Validate long URL

    if (empty($new_long_url)) {
        $errors["long_url"] =  "Enter a link";
    } elseif (! isLinkValid($new_long_url)) {
        $errors["long_url"] = "Please enter a valid link";
    }

    if (empty($new_short_url)) {
        $new_short_url = generateRandomString();
    }

    if (empty($title)) {
        $title = "Untitled";
    }

    // Check if short URL is taken (excluding this current row)
    $check_query = "SELECT id FROM links WHERE short_url = ? AND short_url != ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("ss", $new_short_url, $short_url);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        $errors[] = "Short URL is already taken.";
    }

    // If no errors, update
    if (empty($errors)) {
        $update_query = "UPDATE links SET title = ?, long_url = ?, short_url = ? WHERE short_url = ? AND user_id = ?";
        $stmt = $mysqli->prepare($update_query);
        $stmt->bind_param("ssssi", $title, $new_long_url, $new_short_url, $short_url, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: links.php");
            exit;
        } else {
            $errors[] = "No changes made or unauthorized.";
        }
    }
}

// Fetch existing data to prefill form
if ($short_url && empty($errors)) {
    $query = "SELECT * FROM links WHERE short_url = ? AND user_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("si", $short_url, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $link = $result->fetch_assoc();
    if (!$link) {
        $errors[] = "Link not found.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Link</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles/edit-link.css" rel="stylesheet">
</head>

<body>
    <main>
        <div class="container d-flex justify-content-center align-items-center vh-100">
            <div class="card card-body mx-auto" style="max-width: 500px;">
                    <h1 class="card-title">Edit Link</h1>
                    <?php if (!empty($errors)): ?>
                        <div class="form-errors">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li style="color: red;"><?= htmlspecialchars($error) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="short_url" value="<?= htmlspecialchars($_POST['short_url'] ?? $link['short_url'] ?? '') ?>">
                        <input type="hidden" name="action" value="save">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($_POST['title'] ?? $link['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">

                            <label class="form-label">Destination URL</label>
                            <input type="text" name="long_url" class="form-control" value="<?= htmlspecialchars($_POST['long_url'] ?? $link['long_url'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Custom Short URL</label>
                            <div class="row">
                                <div class="col-auto">
                                    <input type="text" class="form-control" id="inp-def" value="www.snip-url.com" readonly>
                                </div>
                                <div class="col">

                                    <input type="text" name="new_short_url" class="form-control" value="<?= htmlspecialchars($_POST['new_short_url'] ?? $link['short_url'] ?? '') ?>">
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between">              
                            <button class="btn-cancel" class="btn btn-secondary" type="button" onclick="window.location.href='links.php'">Cancel</button>
                            <button class="btn-save" class="btn btn-primary" type="submit">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>




        </div>
    </main>
</body>

</html>