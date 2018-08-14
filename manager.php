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
		if (isset($_GET["part"])==false)
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
		//Login
		elseif ($_GET["part"]=="login"&&isset($_GET["part"]))
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
											<input value=\"Admin Registrieren\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\">
											<input value=\"Über\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=logout\" method=\"POST\">
											<input value=\"Ausloggen\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=parts\" method=\"POST\">
											<input name=\"personnummer\" type=\"text\">
											<input value=\"Schüler Registrieren\" name=\"sregister\" type=\"submit\">
											<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\">
											<input name=\"p1\" value=\"Runde +1\" type=\"submit\">
											<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\">
										</form>";
					}
			}
			elseif (isset($USER)) {
				{
					echo $USER;
					echo  "<form action=\"manager.php?part=register\" method=\"POST\">
										<input value=\"Admin Registrieren\" type=\"submit\">
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
						$mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Uhrzeit`, `Runde`) VALUES ('MAN_".$_POST["name"]."','".$hash."', '', '', '', NULL, NULL)");

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
			if (isset($_POST["sregister"])==true)
			{
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/manager.php?part=anmelden\"', 0);
						</script>";
			}
			elseif (isset($_POST["abmelden"]))
			{
				date_default_timezone_set("Europe/Berlin");
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
						$mysqli->query("UPDATE ".table." SET Anwesenheit='0' , Vorname='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
						echo "Erfolgreich";
						echo "<script type=\"text/javascript\">
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
								</script>";
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			elseif (isset($_POST["p1"]))
			{
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$rounds = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'");
					$rounds = $rounds->fetch_assoc();
					$rounds = $rounds["Runde"];
					$rounds = intval($rounds);
					$rounds = $rounds+1;
					$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' , Uhrzeit='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"',0);
							</script>";
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			elseif (isset($_POST["anmelden"]))
			{
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
						$mysqli->query("UPDATE ".table." SET Anwesenheit='1', Klasse='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
						echo "Erfolgreich";
						echo "<script type=\"text/javascript\">
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
								</script>";
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			else {

					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";

			}
		}
		//Anmelden
		elseif ($_GET["part"]=="anmelden"&&isset($_GET["part"]))
		{
			if (isset($_GET["process"])==false)
			{
					echo 1;
				echo "<script>
				  var txt;
				  var nummer = prompt(\"Schülernummer:\", \"\");
					var Name = prompt(\"Name:\", \"\");
				    if (nummer == null || nummer == \"\"||Name == null || Name == \"\") {
				      alert(\"Abgebrochen\");
						   window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
				    } else {
							window.location.href = \"".url."/manager.php?part=anmelden&process=on&Name=\"+Name+\"&Nummer=\"+nummer;
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
					$mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Uhrzeit`, `Runde`) VALUES ('".$_GET["Name"]."','', '', '".$_GET["Nummer"]."', '0', 0, 0)");
					echo "Wurde abgemeldet";
					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
							</script>";
			}
		}
		elseif ($_GET["part"]=="anmelden"&&isset($_GET["part"]))
		{

		}
		else
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
	?>

</body>
