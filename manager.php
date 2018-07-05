<?php
	include "settings.php";
?>
<head>
	<title><?php echo name; ?></title>
</head>
<body>
	<?php
	//Login
		if (!isset($_POST["username"])or !isset($_POST["userpassword"]) or $_POST["username"]== null or $_POST["userpassword"] == null && !isset($_SESSION["username"]))
		{
			echo "<form action=\"manager.php\" method=\"POST\">
						<p>Name:</p>
						<input name=\"username\" type=\"text\">
						<p>Passwort:</p>
						<input name=\"userpassword\" type=\"password\"><br>
						<input value=\"Anmelden\" type=\"submit\">
				 </form>";
		}
		elseif (isset($_POST["username"])or isset($_POST["userpassword"]) && !isset($_SESSION["username"]))
		{
			$username = $_POST["username"];
			$userpassword = $_POST["userpassword"];
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \")
					</script>");
			}
				$dbpasswd = $mysqli->query("SELECT Vorname FROM ".table." WHERE Name='MAN_".$username."'");
				$dbpasswd = $dbpasswd->fetch_assoc();
				$dbpasswd = $dbpasswd["Vorname"];
		   		if(password_verify($userpassword,$dbpasswd))
				{
					session_start();
					$_SESSION["username"]=$POST["username"];
					show_menu();
				}


		}
	?>

</body>
