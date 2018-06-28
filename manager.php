<?php
	include "settings.php";
?>
<head>
	<title><?php echo name; ?></title>
</head>
<body>
	<?php
		if (!isset($_POST["username"])or !isset($_POST["userpassword"]) or $_POST["username"]== null or $_POST["userpassword"] == null)
		{
			echo "<form action=\"manager.php\" method=\"POST\">
						<p>Name:</p>
						<input name=\"username\" type=\"text\">
						<p>Passwort:</p>
						<input name=\"userpassword\" type=\"password\"><br>
						<input value=\"Anmelden\" type=\"submit\">
				 </form>";
		}
		else
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
					echo "<form action=\"register.php\" method=\"POST\">
								<input value=\"Neuen Nutzer anlegen\" type=\"submit\">
						 </form>";
				}


		}
	?>

</body>
