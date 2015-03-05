<?php
require_once 'check.php';
require_once 'db.php';
function getEventData( $id ) {

}
//echo "hello".$_SESSION['user'];
// Get event details if not already stored
//http://wiki.hashphp.org/PDO_Tutorial_for_MySQL_Developers
/*$db = new PDO('mysql:host=localhost;dbname=ecell;charset=utf8', $user, $pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);*/

/*if( !isset( $_SESSION['events'] ) ) {
	$query = "SELECT * FROM `events`";
	$stmt = $db_connection->prepare($query);
	if(false===$stmt) {
      die('prepare() failed: ' . htmlspecialchars($mysqli->error));
    }
	//$rc = $stmt->bind_param();
	$rc = $stmt->execute();
	if(false===$rc) {
      die('execute() failed: ' . htmlspecialchars($stmt->error));
    }
	$result = $stmt->get_result();
	//echo $result;
	//$result = $result->fetch_array(MYSQLI_ASSOC);
	//$stmt->close();
	//$stmt = $db->query($query);
	$_SESSION['events'] = $result;
	//$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
	//echo var_dump($results);
}*/
//echo var_dump($_SESSION['events']);
?>
<html>
	<head>
		<title>Welcome</title>
	</head>
	<header><div class="cred">
		<span><?php echo "Welcome ".$_SESSION['user']; ?></span>
	</div></header>
	<div class="sidebar">
		<ul class="events">
			<?php
				//tried storing the data to avoid multiple data access in
				//session but failed, fixme!
				$query = "SELECT * FROM `events`";
				$stmt = $db_connection->prepare($query);
				if(false===$stmt) {
			      die('prepare() failed: ' . htmlspecialchars($mysqli->error));
			    }
				//$rc = $stmt->bind_param();
				$rc = $stmt->execute();
				if(false===$rc) {
			      die('execute() failed: ' . htmlspecialchars($stmt->error));
			    }
				$events = $stmt->get_result();
				//$events = $_SESSION['events'];
				//echo var_dump($events);
				while($row = $events->fetch_array(MYSQLI_ASSOC)) {
					?><li><?php echo $row['name'] ?></li><?php
				}
			?>
		</ul>
	</div>
	<div class="main">
		<?php
			$error = "";
			//first check to see if something has been sent by get request
			if( isset( $_GET['evid'] ) ) {
				$id = $_GET['evid'];
				$query = "SELECT * FROM `events` where id=?";
				$stmt = $db_connection->prepare($query);
				if(false===$stmt) {
					die('prepare() failed: ' . htmlspecialchars($mysqli->error));
				}
				$rc = $stmt->bind_param("i",$id);
				$rc = $stmt->execute();
				if(false===$rc) {
					die('execute() failed: ' . htmlspecialchars($stmt->error));
				}
				$event = $stmt->get_result();
				//invalid get request
				if( count($event) < 1 ) {
					$error = "Invalid request";
				}
			}
			if( !empty( $error ) ) {

			} else {

			}
		?>
	</div>
</html>
