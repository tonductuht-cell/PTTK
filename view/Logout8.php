<?php
session_start();
session_destroy();
header('Location: LoginView8.php');
exit;
