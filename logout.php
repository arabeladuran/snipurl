<?php
session_start();
// destoying session removes the user login
session_destroy();

header("Location: index.php");
exit;