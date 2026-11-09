<?php
session_start();

if (!empty($_SESSION['user'])) {
    $destination = $_SESSION['user']['role'] === 'manager'
        ? 'view/Manager8/ManagerMainMenu8.php'
        : 'view/Customer8/CustomerMainMenu8.php';
    header('Location: ' . $destination);
    exit;
}

header('Location: view/LoginView8.php');
exit;
