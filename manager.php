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
				$Anwesend=$mysqli->query("SELECT Klasse FROM ".table." WHERE Name='MAN_".$username."'");
				$Anwesend = $Anwesend->fetch_assoc();
				$Anwesend = $Anwesend["Klasse"];
			//	if($Anwesend==0)
	//			{
					$mysqli->query("UPDATE ".table." SET Klasse=1 WHERE Name='MAN_".$username."'");
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
											<input value=\"SchülerInnen Registrieren\" name=\"sregister\" type=\"submit\">
										</form>
										<form action=\"manager.php?part=parts\" method=\"POST\">
											<input name=\"personnummer\" type=\"text\">
											<select id=\"Oder\" name=\"Oder\">
												<option value=\"K\">Klasse</option>
												<option value=\"SuS\">SchülerInnen</option>
											</select>
											<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\">
											<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\">
											<input name=\"p1\" value=\"Runde +1\" type=\"submit\">
										</form>
										<iframe src=\"Tabellen.php\" height=\"600px\" width=\"50%\" id=\"Vermisst\"></iframe>
										<iframe src=\"TabellenA.php\" height=\"600px\" width=\"50%\" id=\"Allgemein\"></iframe>

										<script>
										function refreshIFrame() {
					            var x = document.getElementById(\"Vermisst\");
											var y = document.getElementById(\"Allgemein\");
					            x.contentWindow.location.reload();
											y.contentWindow.location.reload();
					            var t = setTimeout(refreshIFrame, 60000);
					        	}
										</script>
										";
				}
				/*elseif ($Anwesend==1)
				{
					echo "Bitte melde dich erst an einem Anderem PC mit deinem Account ab!";
				}*/
				else
				{
					echo "Es liegt ein Fehler mit deinem Account vor";
				}

					}

/*			elseif (isset($USER)) {
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
										<input value=\"Schüler Registrieren\" name=\"sregister\" type=\"submit\">
										<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\">
										<input name=\"p1\" value=\"Runde +1\" type=\"submit\">
										<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\">
									</form>";
				}
			}*/
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
						$mysqli->query("UPDATE ".table." SET Anwesenheit='2' , Vorname='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
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
					if ($_POST("Oder")=="SuS")
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
					elseif($_POST["Oder"]=="K")
					{
						$mysqli = new mysqli(host,user, password, database);
						if($mysqli->connect_errno)
						{
							exit("<script type=\"text/javascript\">
									alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
						}
						for ($i=0;$i <=maxschueler; $i++)
						{
							$mysqli->query("UPDATE ".table." SET Anwesenheit='1', Klasse='".time()."' WHERE Nummer='".$i."' AND Klasse='".$_POST["personnummer"]."'");
						}
						echo "Erfolgreich";
						echo "<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
									</script>";
					}
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das Feld aus!\")
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

				echo "<script>
				  var txt;
				  var nummer = prompt(\"Schülernummer:\", \"\");
					var Name = prompt(\"Name:\", \"\");
					var klasse = prompt(\"Klasse:\", \"\");
				    if (nummer == null || nummer == \"\"||Name == null || Name == \"\"||klasse == null||klasse==\"\") {
				      alert(\"Abgebrochen\");
						   window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
				    } else {
							alert( \"".url."/manager.php?part=anmelden&process=on&Name=\"+Name+\"&Nummer=\"+nummer+\"&Klasse=\"+klasse);
							window.location.href = \"".url."/manager.php?part=anmelden&process=on&Name=\"+Name+\"&Nummer=\"+nummer+\"&Klasse\"+klasse;
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
					$mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Ankunftszeit`, `Uhrzeit`, `Runde`) VALUES ('".$_GET["Name"]."', NULL, '".$_GET["Klasse"]."', '".$_GET["Nummer"]."', '', '', NULL, NULL)");
					echo "Wurde abgemeldet";
					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
							</script>";
			}
		}
		elseif ($_GET["part"]=="export"&&isset($_GET["part"]))
		{
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
					</script>");
			}
			$csv = fopen("export(UTF8).csv","w+");
			fwrite($csv,"Nummer,Name,Klasse,Anwesenheit,Uhrzeit,Ankunftszeit\n");
				for($i = 1; $i <= maxschueler; $i++)
				{
					$sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'");
					$sqlSelect=$sqlSelect->fetch_assoc();
					if($sqlSelect["Nummer"]!="")
					{
						fwrite($csv,
						$sqlSelect["Nummer"].","
						.$sqlSelect["Name"].","
						.$sqlSelect["Klasse"].","
						.$sqlSelect["Anwesenheit"].","
						.strftime("%H:%M", $sqlSelect["Uhrzeit"]).","
						.$sqlSelect["Ankunftszeit"]."\n");
					}
				}
				fclose($csv);
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/export.csv\"', 0);
						</script>";
		}
		else
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
	?>

</body>
