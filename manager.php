<?php
	require "settings.php";
	if (is_writable("settings.php")==false)
	{
		die("Keine Schreibrechte auf dem Server Vorhanden.");
	}
	$browser = get_browser(null, true);
	if($browser["cookies"]=="0")
	{
		print_r($browser);
		die("ISPOLASO benötigt zwingend Cookies");
	}
	date_default_timezone_set("Europe/Berlin");
	$time=time()+600;
	session_set_cookie_params("31536000");
?>
<head>
	<title><?php echo name; ?></title>
	<link href="images/icon.png" type="image/png" rel="icon">
</head>
<body>
	<?php
	session_start();
	//Umleitung
		if (isset($_GET["part"])==false&&isset($_SESSION["username"])==false&&session_status()==2)
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
		//Login
		elseif ($_GET["part"]=="login"&&isset($_GET["part"])&&isset($_SESSION["username"])==false&&session_status()==2)
		{
			echo "<head>
					<title>".name."</title>
					<link rel=\"stylesheet\" href=\"Interface.css\">
			</head>
			<body>
			<div id=header>
					<p>".spruch."</p>
				</div>
				<div id=hotbar>
					<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
						<input value=\"Über\" type=\"submit\" id=hbbutt>
					</form>
				</div>
				<div id=content>
					<form action=\"manager.php?part=interface\" method=\"POST\">
						<p id=login>Name:</p>
						<input name=\"username\" type=\"text\">
						<p id=login>Passwort:</p>
						<div>
							<input name=\"userpassword\" type=\"password\">
						</div>
						<div id=login2>
							<input value=\"Anmelden\" type=\"submit\" id=button>
						</div>
					</form>
				</div>
			</body>";
		}
		//Dashboard
		elseif ($_GET["part"]=="interface"&&isset($_GET["part"]))
		{
			if (isset($_SESSION["username"])==false&&session_status()==2)
			{
				if (isset($USER)==false&&isset($_POST["userpassword"])&&isset($_POST["username"])){
					$username = $_POST["username"];
					$userpassword = $_POST["userpassword"];
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$Anwesend=$mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Name='MAN_".$username."'");
					$Anwesend = $Anwesend->fetch_assoc();
					$Anwesend = $Anwesend["Anwesenheit"];
					$Sicherheit = $mysqli->query("SELECT Klasse FROM ".table." WHERE Name='MAN_".$username."'");
					$Sicherheit = $Sicherheit->fetch_assoc();
					$Sicherheit = $Sicherheit["Klasse"];
					if ($Anwesend==0)
					{
						$dbpasswd = $mysqli->query("SELECT Vorname FROM ".table." WHERE Name='MAN_".$username."'");
						$dbpasswd = $dbpasswd->fetch_assoc();
						$dbpasswd = $dbpasswd["Vorname"];
							if(password_verify($userpassword,$dbpasswd))
							{
								$mysqli->query("UPDATE ".table." SET Anwesenheit=1 WHERE Name='MAN_".$username."'");
								$managerLog = fopen("Manager.log", "a");
								fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$username." hat sich erfolgreich angemeldet\n");
								fclose($managerLog);
								$_SESSION["username"] = $username;
								$_SESSION["UStufe"] = $Sicherheit;
								if ($_SESSION["UStufe"]=="1")
								{
									$_SESSION["Button"] = "hbbutt";
								}
								else
								{
									$_SESSION["Button"] = "hbbuttg";
								}

								//interface
								echo  "<head>
												<title>".name."</title>
												<link rel=\"stylesheet\" href=\"Interface.css\">
											</head>

											<body>
												<div id=header>
													<p id=Titel>".spruch."</p>
													<p id=Adminname>Account: ".$_SESSION["username"]."</p>
												</div>
												<div id=hotbar>
													<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
														<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
													</form>
													<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos>
														<input value=\"Admin Registrieren\" type=\"submit\" id=".$_SESSION["Button"].">
													</form>
													<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
														<input value=\"Über\" type=\"submit\" id=hbbutt>
													</form>



													<form action=\"manager.php?part=export\" method=\"POST\" id=hbpos2>
														<input value=\"Daten Exportieren\" type=\"submit\" id=hbbutt>
													</form>

													<form action=\"manager.php?part=parts\" method=\"POST\" id=hbpos>
														<input value=\"Schüler/-innen Registrieren\" name=\"sregister\" type=\"submit\" id=".$_SESSION["Button"].">
													</form>
													</div>
													<div id=content>
													<form action=\"manager.php?part=parts\" method=\"POST\">
														<input name=\"personnummer\" type=\"text\">
														<select id=\"Oder\" name=\"Oder\">
															<option value=\"Nummer\">Schüler/innen</option>
															<option value=\"Klasse\">Klasse</option>
														</select>
														<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\" id=button>
														<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\" id=button>
														<input name=\"p1\" value=\"Runde +1\" type=\"submit\" id=button>
														<input name=\"m1\" value=\"Runde -1\" type=\"submit\" id=button>
													</form>
													<iframe src=\"Tabellen.php\" height=\"600px\" width=\"49%\" id=\"Vermisst\"></iframe>
													<iframe src=\"TabellenA.php\" height=\"600px\" width=\"49%\" id=\"Allgemein\"></iframe>
												</div>
											</body>
												";
					}

					else
					{
						echo "<head>
										<title>".name."</title>
										<link rel=\"stylesheet\" href=\"Interface.css\">
									</head>
									<body>
										<div id=header>
											<p>".spruch."</p>
										</div>
										<div id=hotbar>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
										</div>
										<div id=content2>
											<p id=Fehlermeldung>Falsches Passwort</p>
											<form action=\"manager.php?part=login\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("![%d.%m.%Y_%H:%M]",time())."    ".$_username."konnte nicht angemeldet werden\n");
						fclose($managerLog);
					}
				}
				else
				{
					echo "<head>
									<title>".name."</title>
									<link rel=\"stylesheet\" href=\"Interface.css\">
								</head>
								<body>
									<div id=header>
										<p>".spruch."</p>
									</div>
									<div id=hotbar>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
									</div>
									<div id=content2>
										<p id=Fehlermeldung>Bitte melde dich erst an anderen PCs ab.</p>
										<form action=\"manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
				}
				}
				else
				{
					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
							</script>";
				}
			}
			elseif (isset($_SESSION["username"])==true&&session_status()==2)
			{
				echo  "<head>
								<title>".name."</title>
								<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>

							<body>
								<div id=header>
									<p id=Titel>".spruch."</p>
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos>
										<input value=\"Admin Registrieren\" type=\"submit\" id=".$_SESSION["Button"].">
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>



									<form action=\"manager.php?part=export\" method=\"POST\" id=hbpos2>
										<input value=\"Daten Exportieren\" type=\"submit\" id=hbbutt>
									</form>

									<form action=\"manager.php?part=parts\" method=\"POST\" id=hbpos>
										<input value=\"Schüler/-innen Registrieren\" name=\"sregister\" type=\"submit\" id=".$_SESSION["Button"].">
									</form>
									</div>
									<div id=content>
									<form action=\"manager.php?part=parts\" method=\"POST\">
										<input name=\"personnummer\" type=\"text\">
										<select id=\"Oder\" name=\"Oder\">
											<option value=\"Nummer\">Schüler/innen</option>
											<option value=\"Klasse\">Klasse</option>
										</select>
										<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\" id=button>
										<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\" id=button>
										<input name=\"p1\" value=\"Runde +1\" type=\"submit\" id=button>
										<input name=\"m1\" value=\"Runde -1\" type=\"submit\" id=button>
									</form>
									<iframe src=\"Tabellen.php\" height=\"600px\" width=\"49%\" id=\"Vermisst\"></iframe>
									<iframe src=\"TabellenA.php\" height=\"600px\" width=\"49%\" id=\"Allgemein\"></iframe>
								</div>
							</body>
								";
			}
			else
			{
				echo "<head>
								<title>".name."</title>
								<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
								<div id=header>
									<p>.spruch.</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
									<input value=\"Über\" type=\"submit\" id=hbbutt>
								</form>
								</div>
								<div id=content2>
									<p id=Fehlermeldung>Sessions sind auf diesem Server deaktiviert</p>
									<form action=\"manager.php?part=interface\" method=\"POST\" >
										<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
									</form>
								</div>
							</body>";
				$managerLog = fopen("Manager.log", "a");
				fwrite($managerLog, strftime("!![%d.%m.%Y_%H:%M]",time())."    Session konnte nicht erstellt werden\n");
				fclose($managerLog);
			}
		}
		//Register
		elseif ($_GET["part"]=="register"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&$_SESSION["UStufe"]=="1"||$_SESSION["UStufe"]=="-1"&&session_status()==2)
		{
			if (isset($_GET["process"])==false)
			{
				echo "<head>
									<title>".name."</title>
									<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
								<div id=header>
									<p id=Titel>".spruch."</p>
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=login2>
								<form action=\"manager.php?part=register&process=on\" method=\"POST\">
									<p id=login>Bitte gebe den neuen Nutzername ein:</p>
									<input name=\"name\" type=\"text\">
									<p id=login>Bitte gebe die Sicherheitstufe an:</p>
									<select id=\"Oder\" name=\"Oder\">
										<option value=\"1\">1</option>
										<option value=\"2\">2</option>
										<option value=\"-1\">-1 (Nur MYSQL)</option>
									</select>
									<p id=login>Bitte gebe ein Passwort ein:</p>
									<input name=\"password\" type=\"password\">
									<p id=login>Bitte wiederhole das Passwort :</p>
									<input name=\"password2\" type=\"password\"><br>
								</div>
								<div id=login2>
										<input value=\"Registrieren\" type=\"submit\" id=button>
								</form>
								<form action=\"manager.php?part=interface\" method=\"POST\" id=Rechts>
									<input value=\"Abbrechen\" type=\"submit\" id=button>
								</form>
								</div>
							</body>";
			}
			elseif (isset($_GET["process"])==true)
			{
				if ($_POST["password"]==$_POST["password2"])
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
					}
						$hash =password_hash($_POST["password"],PASSWORD_DEFAULT);
						$rog = $mysqli->query("SELECT Name FROM ".table." WHERE Name='MAN_".$_POST["name"]."'");
						$rog = $rog->fetch_assoc();
						if($rog["Name"]=="")
						{
							$d = $mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Uhrzeit`, `Runde`) VALUES ('MAN_".$_POST["name"]."','".$hash."', '".$_POST["Oder"]."', '', '', 0, 0)");
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat den Admin ".$_POST["name"]." registriert\n");
							fclose($managerLog);
							echo "<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
									</script>";
						}
						else
						{
							echo "<head>
											<title>".name."</title>
											<link rel=\"stylesheet\" href=\"Interface.css\">
										</head>
										<body>
											<div id=header>
												<p id=Titel>".spruch."</p>
												<p id=Adminname>Account:</p>
											</div>
											<div id=hotbar>
												<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
													<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
												</form>
												<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
													<input value=\"Über\" type=\"submit\" id=hbbutt>
												</form>
											</div>
											<div id=content2>
												<p id=Fehlermeldung>Der neue Admin konnte nicht registriert werden.</p>
												<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
													<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
												</form>
											</div>
										</body>" ;
						}

				}
				else
				{
					echo "<head>
									<title>".name."</title>
									<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
								<div id=header>
									<p id=Titel>".spruch."</p>
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
								</div>
								<div id=hotbar>
									<a id=TextHB>Die Passwörter stimmen nicht überein!</a>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>

								</div>
								<div id=login2>
									<form action=\"manager.php?part=register&process=on\" method=\"POST\">
										<p id=login>Bitte gebe den neuen Nutzername ein:</p>
										<input name=\"name\" type=\"text\">
										<p id=login>Bitte gebe die Sicherheitstufe an:</p>
											<select id=\"Oder\" name=\"Oder\">
												<option value=\"1\">1</option>
												<option value=\"2\">2</option>
												<option value=\"-1\">-1 (Nur MYSQL)</option>
											</select>
										<p id=login>Bitte gebe ein Passwort ein:</p>
										<input name=\"password\" type=\"password\">
										<p id=login>Bitte wiederhole das Passwort :</p>
										<input name=\"password2\" type=\"password\"><br>
								</div>
								<div id=login2>
										<input value=\"Registrieren\" type=\"submit\" id=button>
									</form>
									<form action=\"manager.php?part=interface\" method=\"POST\" id=Rechts>
										<input value=\"Abbrechen\" type=\"submit\" id=button>
									</form>
								</div>
							</body>";
				}

			}

		}
		//logout
		elseif ($_GET["part"]=="logout"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2)
		{
			$managerLog = fopen("Manager.log", "a");
			fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat sich abgemeldet\n");
			fclose($managerLog);
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				$managerLog = fopen("Manager.log", "a");
				fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
				fclose($managerLog);
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
					</script>");
			}
			$mysqli->query("UPDATE ".table." SET Anwesenheit=0 WHERE Name='MAN_".$_SESSION["username"]."'");
			session_destroy();
			session_unset();
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=login\"', 0);
					</script>";
		}
		//Über
		elseif ($_GET["part"]=="about"&&isset($_GET["part"]))
		{
			if(isset($_SESSION["username"])==false&&session_status()==2)
			{
				echo "<head>
								<title>".name."</title>
								<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
							<div id=header>
									<p id=Titel>".spruch."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=login\" method=\"POST\" id=hbpos>
										<input value=\"Einloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content>
									<p id=Zentrieren><a id=Fehlermeldung>Programmiert von:</a> <a id=Grau>Jonas Becker</a><br>
									<a id=Fehlermeldung>Design:</a> <a id=Grau>Florian Weichert & Marten Schiwek</a><br>
									<a id=Fehlermeldung>Konzept & Idee:</a> <a id=Grau>Jonas Becker & Marten Schiwek</a><br>
									<a id=Fehlermeldung>ISPOLASO Version 0.4 BETA</a><br></p>
								</div>
							</body>";
			}
			elseif (isset($_SESSION["username"])==true&&session_status()==2)
			{
				echo "<head>
								<title>".name."</title>
								<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
							<div id=header>
									<p id=Titel>".spruch."</p>
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos>
										<input value=\"Zum Dashboard\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"".url."manager.php?part=interface\" method=\"POST\" id=hbpos>
											<input value=\"GitHub Seite\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content>
									<p id=Zentrieren><a id=Fehlermeldung>Programmiert von:</a> <a id=Grau>Jonas Becker</a><br>
									<a id=Fehlermeldung>Design:</a> <a id=Grau>Florian Weichert & Marten Schiwek</a><br>
									<a id=Fehlermeldung>Konzept & Idee:</a> <a id=Grau>Jonas Becker & Marten Schiwek</a><br>
									<a id=Fehlermeldung>ISPOLASO Version 0.4 BETA</a><br></p>
								</div>
							</body>";
			}
		}
		//Parts
		elseif ($_GET["part"]=="parts"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2)
		{
			//Weiterleitung zur Registrierung
			if (isset($_POST["sregister"])==true)
			{
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/manager.php?part=anmelden\"', 0);
						</script>";
			}
			//Abmeldung
			elseif (isset($_POST["abmelden"]))
			{
				date_default_timezone_set("Europe/Berlin");
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					if ($_POST["Oder"]=="Nummer")
					{
						$mysqli = new mysqli(host,user, password, database);
						if($mysqli->connect_errno)
						{
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
							fclose($managerLog);
							exit("<script type=\"text/javascript\">
									alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
						}
						$aog = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Name='MAN_".$_POST["name"]."'");
						$aog = $aog->fetch_assoc();
						if($aog["Anwesenheit"]=="1")
						{
							$mysqli->query("UPDATE ".table." SET Anwesenheit='2' , Vorname='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
							echo "<head>
											<title>".name."</title>
											<link rel=\"stylesheet\" href=\"Interface.css\">
									</head>
									<body>
									<div id=header>
											<p>".spruch."</p>
										</div>
										<div id=hotbar>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content2>
											<p id=Fehlermeldung>Nummer ".$_POST["personnummer"]." wurde erfolgreich abgemeldet</p>
											<form action=\"manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
						}
						elseif($aog["Anwesenheit"]=="2")
						{
							echo "<head>
											<title>".name."</title>
											<link rel=\"stylesheet\" href=\"Interface.css\">
									</head>
									<body>
									<div id=header>
											<p>".spruch."</p>
										</div>
										<div id=hotbar>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content2>
											<p id=Fehlermeldung>Der Schüler ist bereits abgemeldet.</p>
											<form action=\"manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
							}
					else
					{
						echo "<head>
										<title>".name."</title>
										<link rel=\"stylesheet\" href=\"Interface.css\">
								</head>
								<body>
								<div id=header>
										<p>".spruch."</p>
									</div>
									<div id=hotbar>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content2>
										<p id=Fehlermeldung>Der Schüler ist nicht angemeldet oder es liegt ein Fehler vor.</p>
										<form action=\"manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
						}

					}
					elseif ($_POST["Oder"]=="Klasse")
					{
						for ($i=0;$i <=maxschueler; $i++)
						{
							$mysqli = new mysqli(host,user, password, database);
							if($mysqli->connect_errno)
							{
								$managerLog = fopen("Manager.log", "a");
								fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
								fclose($managerLog);
								exit("<script type=\"text/javascript\">
										alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
									</script>");
							}
							$aog = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Name='MAN_".$_POST["name"]."'");
							$aog = $aog->fetch_assoc();
							if($aog["Anwesenheit"]=="1")
							{
								$mysqli->query("UPDATE ".table." SET Anwesenheit='1', Ankunftszeit='".time()."' WHERE Nummer='".$i."' AND Klasse='".$_POST["personnummer"]."'");
							}
							elseif($aog["Anwesenheit"]=="2")
							{
								echo "<script>
												alert(\"Nummer ".$i." ist bereits abgemeldet\");
											<script>";
								}
						else
						{
							echo "<script>
											alert(\"Mit Nummer ".$i." liegt ein Fehler vor\");
										<script>";
						}
					}
					$managerLog = fopen("Manager.log", "a");
					fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat ".$_POST["Oder"].": ".$_POST["personnummer"]." abgemeldet\n");
					fclose($managerLog);
					echo "<head>
									<title>".name."</title>
									<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
							<div id=header>
									<p>".spruch."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content2>
									<p id=Fehlermeldung>Nummer ".$_POST["personnummer"] ."wurde abgemeldet.</p>
									<form action=\"manager.php?part=interface\" method=\"POST\" >
										<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
									</form>
								</div>
							</body>";
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			//plus eine Runde
			elseif (isset($_POST["p1"]))
			{
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$pog = $mysqli->query("SELECT Nummer FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'");
					$pog = $pog->fetch_assoc();
					if($pog["Nummer"]!="")
					{
						$rounds = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'");
						$rounds = $rounds->fetch_assoc();
						$rounds = $rounds["Runde"];
						$rounds = intval($rounds);
						$rounds = $rounds+1;
						$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' , Uhrzeit='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_POST["personnummer"]." eine Runde hinzugefügt\n");
						fclose($managerLog);
						echo "<script type=\"text/javascript\">
									alert(\"Nummer ".$_POST["personnummer"]." wurde eine Runde hinzugefügt\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"',0);
									</script>";
					}
					else
					{
						echo "<head>
										<title>".name."</title>
										<link rel=\"stylesheet\" href=\"Interface.css\">
								</head>
								<body>
								<div id=header>
										<p>".spruch."</p>
									</div>
									<div id=hotbar>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content2>
										<p id=Fehlermeldung>Der Nutzer existiert nicht</p>
										<form action=\"manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
						}
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			//minus eine Runde
			elseif (isset($_POST["m1"]))
			{
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$pog = $mysqli->query("SELECT Nummer FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'");
					$pog = $pog->fetch_assoc();
					if($pog["Nummer"]!="")
					{
						$rounds = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'");
						$rounds = $rounds->fetch_assoc();
						$rounds = $rounds["Runde"];
						$rounds = intval($rounds);
						$rounds = $rounds-1;
						$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' , Uhrzeit='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_POST["personnummer"]." eine Runde hinzugefügt\n");
						fclose($managerLog);
						echo "<script type=\"text/javascript\">
									alert(\"Nummer ".$_POST["personnummer"]." wurde eine Runde hinzugefügt\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"',0);
									</script>";
					}
					else
					{
						echo "<head>
										<title>".name."</title>
										<link rel=\"stylesheet\" href=\"Interface.css\">
								</head>
								<body>
								<div id=header>
										<p>".spruch."</p>
									</div>
									<div id=hotbar>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content2>
										<p id=Fehlermeldung>Der Nutzer existiert nicht</p>
										<form action=\"manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
						}
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			//anmelden
			elseif (isset($_POST["anmelden"]))
			{
				if ($_POST["personnummer"]!=""&&isset($_POST["personnummer"]))
				{
					if ($_POST["Oder"]=="Nummer")
					{
						$mysqli = new mysqli(host,user, password, database);
						if($mysqli->connect_errno)
						{
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
							fclose($managerLog);
							exit("<script type=\"text/javascript\">
									alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
						}
						$Aog = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer=".$_POST["pesonnummer"]);
						$Aog = $Aog->fetch_assoc();
						if ($Aog["Anwesenheit"]!="1")
						{
							$mysqli->query("UPDATE ".table." SET Anwesenheit='1', Ankunftszeit='".time()."' WHERE Nummer='".$_POST["personnummer"]."'");
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_POST["personnummer"]. "angemeldet\n");
							fclose($managerLog);
							echo "Erfolgreich";
							echo "<script type=\"text/javascript\">
											window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
										</script>";
						}
						else
							{
								echo "<head>
												<title>".name."</title>
												<link rel=\"stylesheet\" href=\"Interface.css\">
										</head>
										<body>
										<div id=header>
												<p>".spruch."</p>
											</div>
											<div id=hotbar>
												<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
													<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
												</form>
												<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
													<input value=\"Über\" type=\"submit\" id=hbbutt>
												</form>
											</div>
											<div id=content2>
												<p id=Fehlermeldung>Der Admin existiert Bereits</p>
												<form action=\"manager.php?part=interface\" method=\"POST\" >
													<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
												</form>
											</div>
										</body>";
							}
						}
					}
					elseif($_POST["Oder"]=="Klasse")
					{
						$mysqli = new mysqli(host,user, password, database);
						if($mysqli->connect_errno)
						{
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
							fclose($managerLog);
							exit("<script type=\"text/javascript\">
									alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
								</script>");
						}
						for ($i=0;$i <=maxschueler; $i++)
						{
							$Aog = $mysqli->query("SELECT Anwesenheit FROM ".table."WHERE Nummer='".$i."' AND Klasse='".$_POST["personnummer"]."'");
							$Aog = $mysqli->fetch_assoc();
							if($Aog["Anwesenheit"]!="1")
							{
								$mysqli->query("UPDATE ".table." SET Anwesenheit='1', Ankunftszeit='".time()."' WHERE Nummer='".$i."' AND Klasse='".$_POST["personnummer"]."'");
							}
							else
							{
								echo "<script type=\"text/javascript\">
												alert(\"Schüler Nummer ".$i." ist bereits angemeldet.\")
											</script>";
							}
						}
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat die Klasse ".$_POST["personnummer"]." angemeldet\n");
						fclose($managerLog);
						echo "Erfolgreich";
						echo "<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 10);
									</script>";
					}
				}
				//Für FEHLER
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Bitte fülle das Feld aus!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			//Für fehler
			else {

					echo "<script type=\"text/javascript\">
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";

			}
		}
		//SuS Registrieren
		elseif ($_GET["part"]=="anmelden"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2&&$_SESSION["UStufe"]=="1")
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
							window.location.href = \"".url."/manager.php?part=anmelden&process=on&Name=\"+Name+\"&Nummer=\"+nummer+\"&Klasse=\"+klasse;
				    }
				</script>";
			}
			elseif (isset($_GET["process"])==true)
			{
				$mysqli = new mysqli(host,user, password, database);
				if($mysqli->connect_errno)
				{
					$managerLog = fopen("Manager.log", "a");
					fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
					fclose($managerLog);
					exit("<script type=\"text/javascript\">
							alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
						</script>");
				}
					$result = $mysqli->query("SELECT Nummer FROM ".table." WHERE Nummer='".$_GET["Nummer"]."'");
					$result = $result->fetch_assoc();
					$result = $result["Nummer"];
					$result = intval($result);
					if($result=="")
					{
						$mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Ankunftszeit`, `Uhrzeit`, `Runde`) VALUES ('".$_GET["Name"]."', NULL, '".$_GET["Klasse"]."', '".$_GET["Nummer"]."', '', '', NULL, NULL)");
						$managerLog = fopen("Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_GET["Nummer"]." registriert\n");
						fclose($managerLog);
						echo "<head>
										<title>".name."</title>
										<link rel=\"stylesheet\" href=\"Interface.css\">
									</head>
									<body>
										<div id=header>
											<p>".spruch."</p>
										</div>
										<div id=hotbar>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
										</div>
										<div id=content2>
											<p id=Fehlermeldung>Schüler/innen ".$_GET["Name"]." wurde erfolgreich registriert.</p>
											<form action=\"manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					else
						{
							$managerLog = fopen("Manager.log", "a");
							fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat versucht Nummer ".$_GET["Nummer"]." zu registriert, welches aber existierte\n");
							fclose($managerLog);
							echo "<head>
											<title>".name."</title>
											<link rel=\"stylesheet\" href=\"Interface.css\">
										</head>
										<body>
											<div id=header>
												<p>".spruch."</p>
											</div>
											<div id=hotbar>
												<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
											</div>
											<div id=content2>
												<p id=Fehlermeldung>Nummer ".$_GET["Nummer"]." wurde nicht registriert.</p>
												<form action=\"manager.php?part=interface\" method=\"POST\" >
													<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
												</form>
											</div>
										</body>";
						}
					}
			}
		//Exportieren
		elseif ($_GET["part"]=="export"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2)
		{
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				$managerLog = fopen("Manager.log", "a");
				fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
				fclose($managerLog);
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
					</script>");
			}
			$csv = fopen("Export.csv","w+");
			fwrite($csv,"Nummer,Name,Klasse,Anwesenheit,Uhrzeit,Ankunftszeit,Abmeldezeit,Runde\n");
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
						.$sqlSelect["Ankunftszeit"].","
						.$sqlSelect["Vorname"].","
						.$sqlSelect["Runde"]."\n");
					}
				}
				fclose($csv);
				if (file_exists("Export.zip"))
				{
					unlink("./Export.zip");
				}
				$zipname= "./Export.zip";
				$zip = new ZipArchive();
				if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE)
				{
					$managerLog = fopen("Manager.log", "a");
					fwrite($managerLog, strftime("!![%d.%m.%Y_%H:%M]",time())."    Es gabe einen Fehler beim erstellen der Export.zip\n");
					fclose($managerLog);
				    exit("Es ist ein Fehler beim öffnen der ZIP Datei aufgetreten.\n");
				}
				$InfoTXT = fopen("Info.txt", "a");
				fwrite($InfoTXT, "Erstellt am: ");
				fwrite($InfoTXT, strftime("%d.%m.%Y_%H:%M",time())."\nExport.csv enthält die Datenbank und ist UTF8 kodiert\nManager.log & Client.log enthalten die Logs vom manager Interface und vom der Eingabe GUI\nFalls Logs falsch dargestellt werden einfach in .txt umbenennen");
				fclose($InfoTXT);
					$zip->addFile("Export.csv");
					$zip->addFile("Client.log");
					$zip->addFile("Manager.log");
					$zip->addFile("Info.txt");
					$zip->close();
				$managerLog = fopen("Manager.log", "a");
				fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat die Logs&Tabellen exportiert\n");
				fclose($managerLog);
				echo "<script type=\"text/javascript\">
							window.setTimeout('location.href=\"".url."/Export.zip\"', 0);
						</script>";
				echo "<head>
								<title>".name."</title>
								<link rel=\"stylesheet\" href=\"Interface.css\">
							</head>
							<body>
								<div id=header>
									<p>".spruch."</p>
								</div>
								<div id=hotbar>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
									<input value=\"Über\" type=\"submit\" id=hbbutt>
								</form>
								</div>
								<div id=content2>
									<p id=Fehlermeldung>Download Abgeschlossen</p>
									<form action=\"manager.php?part=interface\" method=\"POST\" >
										<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
									</form>
								</div>
							</body>";
		}
		else
		{
			echo "<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
					</script>";
		}
	?>

</body>
