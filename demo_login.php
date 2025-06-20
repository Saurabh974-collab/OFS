<?php
require 'config.php';
require 'auth.php';

$auth->demoLogin();
header("Location: index.php");
exit();
?>