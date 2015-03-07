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
  //$name = mysql_real_escape_string( $_POST[ 'name' ] );
  //$password = strip_tags( $_POST[ 'password' ] );
  $name = $_POST['name'];
  $query = "SELECT * FROM `users` WHERE name=?";
  try{
    $stmt = $db->prepare($query);
    $stmt->execute(array($name));
  } catch( PDOException $e ) {
      die("Query error ".$e->getMessage());
    }

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);;
  if( empty($result) ) {
    $_SESSION['err'] = "username or password is incorrect";
    //header("Loaction:login.php");
  } else {
    $result = $result[0];
    //$salt = $result['salt'];
    $hash = password_verify($_POST['password'],$result['pass']);
    if( $hash ) {
      $_SESSION['user'] = $name;
      header('Location:profile.php');
    } else {
      $_SESSION['err'] = "username or password is incorrect";
      header("Loaction:login.php");
    }
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
    <div class="err">
			<?php
			if( isset( $_SESSION['err'] ) ) {
			echo $_SESSION['err'];
			unset( $_SESSION['err'] );
			}
			?></div>
		</header>
    <h2 class="heading">Entrepreneurship cell IIT Patna E-Week login</h2>
		<div class="login">
			<h1>Login</h1>
			<form action="login.php" method="post">
				<input class="inp" type="text" name="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];}?>" required="required" placeholder="Username"/>
				<input class="inp" type="password" name="password" required="required" placeholder="Password"/>
				<button class="button" value="Not a member"><a href="register.php">Not a member?</a></button>
        <input class="button" type="submit" value="Login"/>
        <input type="hidden" name = "CSRF" value="<?php $_SESSION['csrf'] = generate_csrf(); echo $_SESSION['csrf']; ?>">
      </form>
		</div>
	</body>
</html>
