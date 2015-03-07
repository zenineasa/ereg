<?php
require_once 'db.php';
require("csrf.php");
session_start();
function errorRedirect( $msg, $url ) {
	$error = $msg;
	session_destroy();
	header('Location:'.$url.'?err='.$error);
}
if( $_SERVER['REQUEST_METHOD'] == "POST") {
  $error = "";
  if($_POST['captcha'] != $_SESSION['digit']) {
  	$error = "Sorry! Inconsistent captcha";
  	$_SESSION['err'] = $error;
  	//errorRedirect($error,"register.php");
  }else if( !check_csrf($_POST['CSRF']) ) {
  	$error = "Sorry! Invalid request";
  	$_SESSION['err'] = $error;
  	//errorRedirect($error,"register.php");
  }else if( !isset( $_POST['name'],$_POST['college'],$_POST['password'],$_POST['email'],$_POST['number'],$_POST['age']) ) {
    $error = "One of the fields is missing";
    $_SESSION['err'] = $error;
    //header("Location:register.php");
  } else if( empty($_POST['name']) || empty($_POST['college']) || empty($_POST['password']) || empty($_POST['email']) || empty($_POST['age']) ) {
  	$error = "One of the fields is missing";
  	$_SESSION['err'] = $error;
    //errorRedirect($error,"register.php");
  } else if( strlen( $_POST['name'] ) > 50 || strlen( $_POST['password'] ) > 50 ) {
    $error = "name or password is too long(max 50)";
    $_SESSION['err'] = $error;
    //errorRedirect($error,"register.php");
    //preg_match( '/^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$/', $_POST[ 'email' ] )
  }else if ( !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) {
    $error = "Invalid email";
    $_SESSION['err'] = $error;
    //errorRedirect($error,"register.php");
  } else if(!filter_var($_POST['age'],FILTER_VALIDATE_INT)) {
  	$error = "Age must be numeric";
  	$_SESSION['err'] = $error;
  } /*else if(!filter_var($_POST['name'],FILTER_VALIDATE_REGEXP,'/[a-zA-Z_\.]/')) {
  	$error = "Only letters,digits underscore and dot allowed in usernames";
  	$_SESSION['err'] = $error;
  }*/
  //$name = mysql_real_escape_string( $_POST[ 'name' ] );
  //$password = strip_tags( $_POST[ 'password' ] );
  //$college = strip_tags( trim( $_POST[ 'college' ] ) );
  //$email = mysql_real_escape_string(trim( $_POST[ 'email' ] ));
  /*if(!(is_int($_POST['number'])&&is_int($_POST['age']))) {
    $error = "Age and number must be numeric";
    $_SESSION['err'] = $error;
    //header("Location:register.php");
  }*/
  //$number = mysql_real_escape_string($_POST['number']);
  //$age = mysql_real_escape_string($_POST['age']);
  //http://www.sanwebe.com/2013/03/basic-php-mysqli-usage
  $name = $_POST['name'];
  $pass = $_POST['password'];
  $query = "SELECT * FROM `users` "
            . "WHERE name=? OR email = ?";
  try{
  	$stmt = $db->prepare($query);
  	$stmt->execute(array($_POST['name'],$_POST['email']));
  } catch( PDOException $e ) {
  	echo "Query error ".$e->getMessage();
  }

  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
  if (count($result) != 0) {
        $error = "Username or email already taken! Please choose another!".var_dump($result);
        $_SESSION['err'] = $error;
        //errorRedirect($error,"register.php");
        //header("Location:register.php");
    }
  if(!isset($_SESSION['err'])){
    $len = 32;
    //$salt = mcrypt_create_iv($len);
    $hash = password_hash($pass,PASSWORD_DEFAULT);
    $query = "INSERT INTO `users` (`name`,`email`,`college`,`number`,`age`,`pass`) ".
    "VALUES (?,?,?,?,?,?)";
    //http://stackoverflow.com/questions/2552545/mysqli-prepared-statements-error-reporting
    try {
    	$stmt = $db->prepare($query);
    	$stmt->execute(array($_POST['name'],
    		$_POST['email'],
    		$_POST['college'],
    		$_POST['number'],
  			$_POST['age'],$hash
    	));
    } catch( PDOException $e ) {
    	echo "Query error ".$e->getMessage();
    }
    if($db->errorCode()!='0') {
    	$_SESSION['err'] = "Registration error ".$db->errorCode();
    } else {
    	//echo var_dump($db->errorInfo());
	    $_SESSION['user'] = $name.'    ';
	    //echo $stmt->error;
	    //echo "Done";
	    $mailTo = $_POST['email'];
	    $txt = "You have been successfully registered for eweek.\nYour username is".$name;
	    $txt .= "\nYour password is ".$pass;
	    $sub = "IIT Patna E-Week registrations";
	    mail($mailTo,$sub,$txt);
	    header('Location:profile.php');
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
	<title>Register</title>
	<link rel="stylesheet" href="regstyle.css">
</head>
<body>
<header>
  <div class = "err">
    <?php
      if( isset( $_SESSION['err'] ) ) {
        echo $_SESSION['err'];
        unset( $_SESSION['err'] );
      }
    ?>
  </div>
</header>
	<div class="box">
	  <form action="register.php" method="post">
		<ul class="list">
			<li class="main"><strong>Registration Form:</strong></li>
			<li>Name: <input class="inp" type="text" name="name" value="<?php if(isset($_POST['name'])){echo $_POST['name'];} ?>"/></li>
			<li>Password: <input class="inp" type="password" name ="password"/></li>
			<li>College: <input class="inp" type="text" name="college" value="<?php if(isset($_POST['college'])){echo $_POST['college'];}?>"/></li>
			<li>Email: <input class="inp" type="email" name="email" value="<?php if(isset($_POST['name'])){echo $_POST['email'];}?>"/></li>
			<li>Number: <input class="inp" type="number" name="number" value="<?php if(isset($_POST['name'])){echo $_POST['number'];}?>"/></li>
			<li>Age: <input class="inp" type="number" name="age" value="<?php if(isset($_POST['name'])){echo $_POST['age'];}?>"/></li>
			<li><img src="./captcha.php" width="120" height="30" border="1" alt="CAPTCHA">
			<input class="inp" type="text" size="6" maxlength="5" name="captcha" value="">
			</li>
			<input type="hidden" name = "CSRF" value="<?php $_SESSION['csrf'] = generate_csrf(); echo $_SESSION['csrf']; ?>">
		</ul>
		<center><input class="button" type="submit" value="Register"/></center>
	</form>
	</div>

</body>
</html>
