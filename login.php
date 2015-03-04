<?php
require_once 'db.php';
session_start();
if( $_SERVER['REQUEST_METHOD'] == "POST") {
  if( !isset( $_POST['name'],$_POST['password'] ) ) {
    $_SESSION['err'] = "Please enter both fields";
    header("Loaction:login.php");
  }
  $name = mysql_real_escape_string( $_POST[ 'name' ] );
  $password = strip_tags( $_POST[ 'password' ] );

  $query = "SELECT (*) FROM `users` WHERE name=?";
  $stmt = $db_connection->prepare($query);
  $rc = $stmt->bind_param("s", $name);
  $rc = $stmt->execute();
  $result = $stmt->get_result();
  $result = $result->fetch_array();
  $stmt->close();
  if( empty($result) ) {
    $_SESSION['err'] = "username or password is incorrect";
    //header("Loaction:login.php");
  }
  $salt = $result['salt'];
  $hash = crypt($pass, $salt);
  if( $hash === $result['hash'] ) {
    $_SESSION['user'] = $name;
    header('Location:profile.php');
  } else {
    $_SESSION['err'] = "username or password is incorrect $hash";
    header("Loaction:login.php");
  }
}
?>
<html>
	<head>
		<title>Login</title>
		<link rel="stylesheet" href="loginstyle.css" />
	</head>
	<body>
		<header>
			<?php
			if( isset( $_SESSION['err'] ) ) {
			echo $_SESSION['err'];
			unset( $_SESSION['err'] );
			}
			?>
		</header>
		<div class="login">
			<h1>Login</h1>
			<form action="login.php" method="post">
				<input class="inp" type="text" name="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];}?>" required="required" placeholder="Username"/>
				<input class="inp" type="password" name="password" required="required" placeholder="Password"/>
				<input class="button" type="submit" value="Login"/>
			</form>
		</div>
	</body>
</html>
