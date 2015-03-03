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
    <div class = "login">
      <form action = "login.php" method = "post">
        <label for = "name" >Username</label><input type = "text" name="name" value = "<?php if(isset($_POST['name'])){echo $_POST['name'];}?>"/>
        <label for = "password">Password</label><input type = "password" name = "password"/>
        <input type = "submit" value = "submit"/>
      </form>
    </div>
  </body>
</html>
