<?php
require_once 'check.php';
require_once 'db.php';
unset($_SESSION['user']);
session_destroy();
header('Location:login.php?err="You have been logged out"');
