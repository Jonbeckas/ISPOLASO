<?php
	require "settings.php"
?>
<head>
	<title><?php echo name; ?></title>
	<link href="images/icon.png" type="image/png" rel="icon">
</head>
<body>
	<?php
	//Umleitung
		if (isset($_GET["part"])==false&&isset($USER)==true)
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
		//Login
		elseif ($_GET["part"]=="login"&&isset($_GET["part"])||isset($USER))
		{
			echo "<form action=\"manager.php?part=interface\" method=\"POST\">
						<p>Name:</p>
						<input name=\"username\" type=\"text\">
						<p>Passwort:</p>
						<input name=\"userpassword\" type=\"password\"><br>
						<input value=\"Anmelden\" type=\"submit\">
				 </form>";
		echo isset($USER);
		}
		//Dashboard
		elseif ($_GET["part"]=="interface"&&isset($_GET["part"]))
		{

			if (isset($USER)==false&&isset($_POST["userpassword"])&&isset($_POST["username"])){
				$username = $_POST["username"];
				$userpassword = $_POST["userpassword"];
				$mysqli = new mysqli(host,user, password, database);
				if($mysqli->connect_errno)
				{
					exit("<script type=\"text/javascript\">
							alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
						</script>");
				}
					$dbpasswd = $mysqli->query("SELECT Vorname FROM ".table." WHERE Name='MAN_".$username."'");
					$dbpasswd = $dbpasswd->fetch_assoc();
					$dbpasswd = $dbpasswd["Vorname"];
			   		if(password_verify($userpassword,$dbpasswd))
					{
						$USER = $username;
						//interface
						echo  "<form action=\"manager.php?part=register\" method=\"POST\">
											<input value=\"Registrieren\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\">
											<input value=\"Über\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=logout\" method=\"POST\">
											<input value=\"Ausloggen\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=parts\" method=\"POST\">
											<input name=\"personnummer\" type=\"text\">
											<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\">
											<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\">
											<input name=\"p1\" value=\"Runde +1\" type=\"submit\">
											<input name=\"bearbeiten\" value=\"Bearbeiten\" type=\"submit\">
										</form>";
				echo isset($USER);
					}
			}
			elseif (isset($USER)) {
				{
					echo $USER;
					echo  "<form action=\"manager.php?part=register\" method=\"POST\">
										<input value=\"Registrieren\" type=\"submit\">
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\">
										<input value=\"Über\" type=\"submit\">
									</form>
									<form action=\"manager.php?part=logout\" method=\"POST\">
										<input value=\"Ausloggen\" type=\"submit\">
									</form>
									<form action=\"manager.php?part=parts\" method=\"POST\">
										<input name=\"personnummer\" type=\"text\">
										<input value=\"Anmelden\" type=\"submit\">
										<input value=\"Abmelden\" type=\"submit\">
										<input value=\"Runde +1\" type=\"submit\">
										<input value=\"Bearbeiten\" type=\"submit\">
									</form>";
				}
			}
			else
			{
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
						</script>";
			}
		}
		//Register
		elseif ($_GET["part"]=="register"&&isset($_GET["part"]))
		{
			if (isset($_GET["process"])==false)
			{
				echo "<form action=\"manager.php?part=register&process=on\" method=\"POST\">
								<p>Bitte gebe den neuen Nutzername ein:</p>
								<input name=\"name\" type=\"text\">
								<p>Bitte gebe ein Passwort ein:</p>
								<input name=\"password\" type=\"password\">
								<p>Bitte wiederhole das Passwort :</p>
								<input name=\"password2\" type=\"password\"><br>
								<input value=\"Registrieren\" type=\"submit\">
						 </form>
						 <form action=\"manager.php?part=interface\" method=\"POST\">
								<input value=\"Abbrechen\" type=\"submit\">
						</form>";
			}
			elseif (isset($_GET["process"])==true)
			{
				if ($_POST["password"]==$_POST["password2"])
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
					}
						$hash =password_hash($_POST["password"],PASSWORD_DEFAULT);
						$mysqli->query("INSERT INTO `schueler` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Uhrzeit`, `Runde`) VALUES ('MAN_".$_POST["name"]."','".$hash."', '', '', '', NULL, NULL)");

				}
				else
				{
					echo "<form action=\"manager.php?part=register&process=on\" method=\"POST\">
									<p color=\"#FF0000\">Die Passwörter stimmen nicht überein!</p><br>
									<p>Bitte gebe den neuen Nutzername ein:</p>
									<input name=\"name\" type=\"text\">
									<p>Bitte gebe ein Passwort ein:</p>
									<input name=\"password\" type=\"password\">
									<p>Bitte wiederhole das Passwort :</p>
									<input name=\"password2\" type=\"password\"><br>
									<input value=\"Registrieren\" type=\"submit\">
							 </form>
							 <form action=\"manager.php?part=interface\" method=\"POST\">
									<input value=\"Abbrechen\" type=\"submit\">
							</form>";
				}

			}

		}
		//logout
		elseif ($_GET["part"]=="logout "&&isset($_GET["part"]))
		{
			unset($USER);
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
		//Über
		elseif ($_GET["part"]=="about"&&isset($_GET["part"]))
		{
			echo "<p>Programmiert von: Jonas Becker<br>Design: Florian Weichert<br>ISPOLASO<br><a href=\"".url.".manager.php?part=interface\">Zurück zum Dashboard</a></p>";
		}
		//Parts
		elseif ($_GET["part"]=="parts"&&isset($_GET["part"]))
		{
			if (isset($_GET["anmelden"]))
			{
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/manager.php?part=anmelden\"', 0);
						</script>";
			}
			elseif (isset($_POST["abmelden"]))
			{

			}
			elseif (isset($_POST["p1"]))
			{

			}
			elseif (isset($_POST["bearbeiten"]))
			{

			}
			else {
				{
					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
		}
		elseif ($_GET["part"]=="anmelden"&&isset($_GET["part"]))
		{
			if (isset($_GET["process"])==false)
			{
				echo "<script>
				  var txt;
				  var nummer = prompt(\"Schülernummer:\", \"\");
					var Name = prompt(\"Name:\", \"\");
					var Nachname = prompt(\"Nachname:\", \"\");
					var Klasse = prompt(\"Klasse:\", \"\");
				    if (nummer == null || nummer == \"\"||Name == null || Name == \"\"||Nachname == null || Nachname == \"\"||Klasse == null || Klasse == \"\") {
				      alert(\"Abgebrochen\");
						   window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
				    } else {
							link =\"/manager.php?part=interface&process=on&Name=\"+Name+\"&Nachname=\"+Nachname+\"&Nummer=\"+nummer+\"&Klasse=\"+Klasse;
							window.setTimeout('location.href=''".url."+link, 0);
				    }
				</script>";
			}
			elseif (isset($_GET["process"])==true)
			{
				$mysqli = new mysqli(host,user, password, database);
				if($mysqli->connect_errno)
				{
					exit("<script type=\"text/javascript\">
							alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
						</script>");
				}
					$dbpasswd = $mysqli->query("INSERT INTO `schueler` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Uhrzeit`, `Runde`) VALUES ('MAN_".$_POST["name"]."','".$hash."', '', '', '', NULL, NULL)");
			}
		}
		else
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
	?>

</body>
