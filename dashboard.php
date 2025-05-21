<?php
session_start();
$mysqli = require __DIR__ . "/database.php";

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

function isLinkValid(&$long_url) {
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

    $long_url = trim($_POST["long-url"]);
    $short_url = trim($_POST["short-url"]);
    // $title = trim($_POST["title"]);
    $user_id = $_SESSION["user_id"];

    $errors = [];

    // form validation
    if (empty($long_url)) {
        $errors["long_url"] =  "Enter a link";
    } elseif (! isLinkValid($long_url)) {
        $errors["long_url"] = "Please enter a valid link";
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

            $query = "INSERT INTO links (user_id, long_url, short_url, created_at) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("isss", $user_id, $long_url, $short_url, $created_at);
            $stmt->execute();

            $success = "Link created!";
        }
    }

    $query = "";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="styles/global.css">
</head>

<body>
    <main>
        <div>
            <h1>Home</h1>
            <form method="post">
                <!-- Link to shorten -->
                <h2>Quick Create</h2>
                <div class ="db-container">
                    <div class="container">
                        <p> <label class="db-txt" for="long-url">Enter long URL</label> </p>
                        <input type="text" id="long-url" name="long-url" placeholder="e.g. https://example.com/longlink"
                        value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                    </div>
                    <?php if (isset($errors["long_url"])): ?>
                        <em class="invalid"><?= $errors["long_url"] ?></em>
                    <?php endif; ?>
                    <div>
                        <p> <label class="db-txt" for="title">Title (Optional)</label> </p>
                        <input type="text" id="title" name="title"
                        value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                    </div>
                    <div>
                        <p> <label for="short-url">Custom URL</label> </p>
                        <div class="db-def-url">
                            <input type="text" id="default-url" value="www.snip-url.com" readonly>
                            <span> / </span>
                            <input type="text" id="short-url" name="short-url"
                            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>">
                        </div>
                    </div>
              
                <?php if (isset($errors["short_url"])): ?>
                    <em class="invalid"><?= $errors["short_url"] ?></em>
                <?php endif; ?>
                
                <?php if (isset($success)): ?>
                    <em class="invalid"><?= $success ?></em>
                    <a href="">localhost/SnipURL/<?=$short_url?></a>
                <?php endif; ?>

                <button class="btn" id="form-btn">Snip your link</button>
                 </form>
            </div>
        </div>

    </main>
</body>

</html>