<?php
session_start();
if( !isset( $_SESSION['user'] ) ) {
  $error = "Not logged in!";
  header('Location:login.php?err="Not Logged In"');
}
function logout(){
	unset($_SESSION['user']);
	session_destroy();
}
