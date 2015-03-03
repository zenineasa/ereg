<?php
session_start();
if( !isset( $_SESSION['user'] ) ) {
  $error = "Not logged in!";
  header('Location:register.php');
}
