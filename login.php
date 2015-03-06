<?php
require_once 'db.php';
require_once 'csrf.php';
session_start();
if( $_SERVER['REQUEST_METHOD'] == "POST" ) {
  if( !check_csrf($_POST['CSRF']) ) {
    $error = "Sorry! Invalid request";
    $_SESSION['err'] = $error;
    //errorRedirect($error,"register.php");
  }else if( !isset( $_POST['name'],$_POST['password'] ) ) {
    $_SESSION['err'] = "Please enter both fields";
  } else if( empty( $_POST['name'] ) || empty($_POST['password']) ) {
    $_SESSION['err'] = "Fields cannot be empty";
  }
  $name = mysql_real_escape_string( $_POST[ 'name' ] );
  $password = strip_tags( $_POST[ 'password' ] );

  $query = "SELECT * FROM `users` WHERE name=?";
  $stmt = $db_connection->prepare($query);
  if(!$stmt) {
    die("Failed to prepare");
  }
  $rc = $stmt->bind_param("s",$name);
  $rc = $stmt->execute();
  $result = $stmt->get_result();
  $result = $result->fetch_array( MYSQLI_ASSOC );
  $stmt->close();
  if( empty($result) ) {
    $_SESSION['err'] = "username or password is incorrect";
    //header("Loaction:login.php");
  }
  $salt = $result['salt'];
  $hash = hash('sha256',$pass.$salt);
  if( $hash === $result['pass'] ) {
    $_SESSION['user'] = $name;
    header('Location:profile.php');
  } else {
    $_SESSION['err'] = "username or password is incorrect $hash ".$result['pass'];
    //header("Loaction:login.php");
  }
} else if( $_SERVER['REQUEST_METHOD'] === "GET" ) {
  if( isset( $_GET['err'] ) ) {
    $_SESSION['err'] = $_GET['err'];
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
    <h2 class="heading">Entrepreneurship cell IIT Patna E-Week login</h2>
		<div class="login">
			<h1>Login</h1>
			<form action="login.php" method="post">
				<input class="inp" type="text" name="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];}?>" required="required" placeholder="Username"/>
				<input class="inp" type="password" name="password" required="required" placeholder="Password"/>
				<input class="button" type="submit" value="Login"/>
        <input type="hidden" name = "CSRF" value="<?php $_SESSION['csrf'] = generate_csrf(); echo $_SESSION['csrf']; ?>">
      </form>
		</div>
	</body>
</html>
