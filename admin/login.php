<?php
session_start();

// Static credentials
$validUsername = 'admin';
$validPassword = 'password';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $validUsername && $password == $validPassword) {
        $_SESSION['loggedin'] = true;
        header("Location: category.php");
        exit();
    } else {
        header("Location: index.php?error=1");
        exit();
    }
}
?>
