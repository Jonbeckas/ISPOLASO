<?php
	require "settings.php";
	if (is_writable("settings.php")==false)
	{
		die("Keine Schreibrechte auf dem Server Vorhanden.");
	}
	if(file_exists("./Logs/")==false)
	{
		mkdir("./Logs/");
		$clientLog = fopen("./Logs/.htaccess", "a");
		fwrite($clientLog, "<Files \"*.*\">\nDeny from all\n</Files>");
		fclose($clientLog);
	}
	$browser = get_browser(null, true);
	if($browser["cookies"]=="0")
	{
		print_r($browser);
		die("ISPOLASO benötigt zwingend Cookies");
	}
	date_default_timezone_set("Europe/Berlin");
	session_set_cookie_params("31536000");
?>
<head>
	<title><?php echo name; ?></title>
	<link href="images/icon.png" type="image/png" rel="icon">
	<meta charset="UTF-8">
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
							<p id=Titel>".spruch."</p>
						</div>
						<div id=hotbar>
							<a id=TextHB>Login</a>
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
				if (isset($USER)==false&&isset($_POST["userpassword"])&&isset($_POST["username"])&&$_POST["username"]!=""&&$_POST["userpassword"]!="")
				{
					$username = $_POST["username"];
					$userpassword = $_POST["userpassword"];
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
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
								$managerLog = fopen("./Logs/Manager.log", "a");
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
								if ($_SESSION["UStufe"]!="-1")
								{
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
														<a id=TextHB>Dashboard</a>
														<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
															<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
														</form>
														<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos2>
															<input value=\"Admin Registrieren\" type=\"submit\" id=".$_SESSION["Button"].">
														</form>
														<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
															<input value=\"Über\" type=\"submit\" id=hbbutt>
														</form>
														<form action=\"manager.php?part=export\" method=\"POST\" id=hbpos2>
															<input value=\"Daten Exportieren\" type=\"submit\" id=hbbutt>
														</form>

														<form action=\"manager.php?part=anmelden\" method=\"POST\" id=hbpos>
															<input value=\"Schüler/-in registrieren\" name=\"sregister\" type=\"submit\" id=".$_SESSION["Button"].">
														</form>
													</div>
													<div id=content>
														<p id=login>Anmeldung</p>
														<form action=\"manager.php?part=parts\" method=\"POST\">
															<input name=\"personnummer\" type=\"text\" placeholder=\"Nummer eingeben\" id=EingabeDB>
															<select id=\"Oder\" name=\"Oder\">
																<option value=\"Nummer\">Schüler/-in</option>
																<option value=\"Klasse\">Klasse</option>
															</select>
															<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\" id=button>
															<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\" id=button2>
															<input name=\"p1\" value=\"Runde +1\" type=\"submit\" id=button>
															<input name=\"m1\" value=\"Runde -1\" type=\"submit\" id=button2>
														</form>
													</div>
													<div id=content2>
														<div id=TitelTabl>
															<p id=iFrameV>Auffällig lange abwesend</p>
															<p id=iFrameA>Übersichtstabelle</p>
															<p id=Grau2>Die Tabellen aktualisieren sich automatisch und brauchen eine Weile, bis sie angezeigt werden.</p>
														</div>
														<div id=iFrameV>
															<iframe src=\"Tabellen.php\" height=\"600px\" id=\"Vermisst\"></iframe>
														</div>
														<div id=iFrameA>
															<iframe src=\"TabellenA.php\" height=\"600px\" id=\"Allgemein\"></iframe>
														</div>
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
												<p id=Titel>".spruch." HOHE RECHTE</p>
												<p id=Adminname>Account: ".$_SESSION["username"]."</p>
											</div>
											<div id=hotbar>
												<a id=TextHB>Dashboard</a>
												<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
													<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
												</form>
												<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
													<input value=\"Über\" type=\"submit\" id=hbbutt>
												</form>
											</div>
											<div id=content3>
												<p id=Fehlermeldung>MYSQL Eingabe:</p>
												<form action=\"manager.php?part=mysql\" method=\"POST\" >
													<input name=\"befehl\" type=\"Text\" placeholder=\"SELECT * FROM ".table."\" id=InputRegi>
													<input name=\"\" value=\"Okay\" type=\"submit\" id=button>
												</form>
											</div>
										</body>";
						}
					}

					else
					{
						echo "echo <head>
												<title>".name."</title>
												<link rel=\"stylesheet\" href=\"Interface.css\">
											</head>
											<body>
											<div id=header>
													<p id=Titel>".spruch."</p>
												</div>
												<div id=hotbar>
													<a id=TextHB>Login-Falsches Passwort/Falscher Benutzername</a>
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
						$managerLog = fopen("./Logs/Manager.log", "a");
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
										<p id=Titel>".spruch."</p>
										<p id=Adminname>Login</p>
									</div>
									<div id=hotbar>
										<a id=TextHB>Nachricht</a>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content3>
										<p id=Fehlermeldung>Bitte melden sie sich erst mit dem Accound von anderen PCs ab!</p>
										<form action=\"".url."/manager.php?part=login\" method=\"POST\" >
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
				if ($_SESSION["UStufe"]!="-1")
				{
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
										<a id=TextHB>Dashboard</a>
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

										<form action=\"manager.php?part=anmelden\" method=\"POST\" id=hbpos>
											<input value=\"Schüler/-in registrieren\" name=\"sregister\" type=\"submit\" id=".$_SESSION["Button"].">
										</form>
									</div>
									<div id=content>
										<p id=login>Anmeldung</p>
										<form action=\"manager.php?part=parts\" method=\"POST\">
											<input name=\"personnummer\" type=\"text\" placeholder=\"Nummer eingeben\" id=EingabeDB>
											<select id=\"Oder\" name=\"Oder\">
												<option value=\"Nummer\">Schüler/-innen</option>
												<option value=\"Klasse\">Klasse</option>
											</select>
											<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\" id=button>
											<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\" id=button2>
											<input name=\"p1\" value=\"Runde +1\" type=\"submit\" id=button>
											<input name=\"m1\" value=\"Runde -1\" type=\"submit\" id=button2>
										</form>
									</div>
									<div id=content2>
										<div id=TitelTabl>
											<p id=iFrameV>Auffällig lange abwesend</p>
											<p id=iFrameA>Übersichtstabelle</p>
											<p id=Grau2>Die Tabellen aktualisieren sich automatisch und brauchen eine Weile, bis sie angezeigt werden.</p>
										</div>
										<div id=iFrameV>
											<iframe src=\"Tabellen.php\" height=\"600px\" id=\"Vermisst\"></iframe>
										</div>
										<div id=iFrameA>
											<iframe src=\"TabellenA.php\" height=\"600px\" id=\"Allgemein\"></iframe>
										</div>
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
								<p id=Titel>".spruch." HOHE RECHTE</p>
								<p id=Adminname>Account: ".$_SESSION["username"]."</p>
							</div>
							<div id=hotbar>
								<a id=TextHB>Dashboard</a>
								<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
									<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
								</form>
								<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
									<input value=\"Über\" type=\"submit\" id=hbbutt>
								</form>
							</div>
							<div id=content3>
								<p id=Fehlermeldung>MYSQL Eingabe:</p>
								<form action=\"manager.php?part=mysql\" method=\"POST\" >
									<input name=\"befehl\" type=\"Text\" placeholder=\"SELECT * FROM ".table."\" id=InputRegi>
									<input name=\"\" value=\"Okay\" type=\"submit\" id=button>
								</form>
							</div>
						</body>";
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
									<p id=Adminname>Login</p>
								</div>
								<div id=hotbar>
									<a id=TextHB>Nachricht</a>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content3>
									<p id=Fehlermeldung>Es konnte keine neue SESSION erstellt werden</p>
									<form action=\"".url."/manager.php?part=login\" method=\"POST\" >
										<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
									</form>
								</div>
							</body>";
				$managerLog = fopen("./Logs/Manager.log", "a");
				fwrite($managerLog, strftime("!![%d.%m.%Y_%H:%M]",time())."    Session konnte nicht erstellt werden\n");
				fclose($managerLog);
			}
		}
		//Register
		elseif ($_GET["part"]=="register"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&$_SESSION["UStufe"]=="1"||$_GET["part"]=="register"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&$_SESSION["UStufe"]=="-1"&&isset($_SESSION["UStufe"])&&session_status()==2)
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
									<a id=TextHB>Admin registrieren</a>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=login2>
									<form action=\"manager.php?part=register&process=on\" method=\"POST\">
										<p id=login>Fülle alle Felder aus und drücke \"Registrieren\":</p>
										<input name=\"name\" type=\"text\" placeholder=\"Bitte gebe den neuen Nutzername ein:\" id=InputRegi>
										<div id=login2>
											<input name=\"password\" type=\"password\" placeholder=\"Bitte gebe ein Passwort ein:\" id=InputRegi>
										</div>
										<div id=login2>
											<input name=\"password2\" type=\"password\" placeholder=\"Bitte wiederhole das Passwort:\" id=InputRegi><br>
										</div>
										<p id=login>Bitte gebe die Sicherheitstufe an:</p>
											<select id=\"Oder\" name=\"Oder\">
												<option value=\"1\">1</option>
												<option value=\"2\">2</option>
												<option value=\"-1\">-1</option>
											</select>
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
						$managerLog = fopen("./Logs/Manager.log", "a");
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
							$managerLog = fopen("./Logs/Manager.log", "a");
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
												<p id=Adminname>Account: ".$_SESSION["username"]."</p>
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
										<a id=TextHB>Admin registrieren | Die Passwörter stimmen nicht überein!</a>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=login2>
										<form action=\"manager.php?part=register&process=on\" method=\"POST\">
											<p id=login>Fülle alle Felder aus und drücke \"Registrieren\":</p>
												<input name=\"name\" type=\"text\" placeholder=\"Bitte gebe den neuen Nutzername ein:\" id=InputRegi>
											<div id=login2>
												<input name=\"password\" type=\"password\" placeholder=\"Bitte gebe ein Passwort ein:\" id=InputRegi>
											</div>
											<div id=login2>
												<input name=\"password2\" type=\"password\" placeholder=\"Bitte wiederhole das Passwort:\" id=InputRegi><br>
											</div>
											<p id=login>Bitte gebe die Sicherheitstufe an:</p>
											<select id=\"Oder\" name=\"Oder\">
												<option value=\"1\">1</option>
												<option value=\"2\">2</option>
												<option value=\"-1\">-1</option>
											</select>
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
			$managerLog = fopen("./Logs/Manager.log", "a");
			fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat sich abgemeldet\n");
			fclose($managerLog);
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				$managerLog = fopen("./Logs/Manager.log", "a");
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
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
								</div>
								<div id=hotbar>
									<a id=TextHB>Über</a>
									<form action=\"manager.php?part=login\" method=\"POST\" id=hbpos2>
										<input value=\"Zum Login\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"https://github.com/Jonbeckas/ISPOLASO\" id=hbpos>
											<input value=\"GitHub Seite\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"https://github.com/Jonbeckas/ISPOLASO/wiki/Übersicht\" id=hbpos>
										<input value=\"ISPOLASO Wiki\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content>
									<p id=Zentrieren><a id=Fehlermeldung>Programmiert von:</a> <a id=Grau>Jonas Becker</a><br>
									<a id=Fehlermeldung>Design:</a> <a id=Grau>Florian Weichert & Marten Schiwek</a><br>
									<a id=Fehlermeldung>Konzept & Idee:</a> <a id=Grau>Jonas Becker & Marten Schiwek</a><br>
									<a id=Fehlermeldung>ISPOLASO Version 0.8</a><br></p>
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
									<a id=TextHB>Über</a>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=interface\" method=\"POST\" id=hbpos>
										<input value=\"Zum Dashboard\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"https://github.com/Jonbeckas/ISPOLASO\" id=hbpos>
											<input value=\"GitHub Seite\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"https://github.com/Jonbeckas/ISPOLASO/wiki/Übersicht\" id=hbpos>
										<input value=\"ISPOLASO Wiki\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content>
									<p id=Zentrieren><a id=Fehlermeldung>Programmiert von:</a> <a id=Grau>Jonas Becker</a><br>
									<a id=Fehlermeldung>Design:</a> <a id=Grau>Florian Weichert & Marten Schiwek</a><br>
									<a id=Fehlermeldung>Konzept & Idee:</a> <a id=Grau>Jonas Becker & Marten Schiwek</a><br>
									<a id=Fehlermeldung>ISPOLASO Version 0.8</a><br></p>
								</div>
							</body>";
			}
		}
		//Parts
		elseif ($_GET["part"]=="parts"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2)
		{
			//Auf leere Testen
			if ($_POST["personnummer"]!="")
			{
				//Plus eine Runde
				if (isset($_POST["p1"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$anwesend = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
					if ($anwesend == "1")
					{
						$runden = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
						$runden = $runden["Runde"]+1;
						$mysqli->query("UPDATE ".table." SET Runde='".$runden."' WHERE Nummer=".$_POST["personnummer"]);
						echo "<script type=\"text/javascript\">
									alert(\"Dem Schüler/ Der Schülerin wurde eine Runde hinzugefügt.\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
					//fals nicht anwesend
					else
					{
						echo "<script type=\"text/javascript\">
									alert(\"Bitte melden sie den Schüler/ die Schülerin erst an!\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
				}
				if (isset($_POST["m1"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$anwesend = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
					if ($anwesend == "1")
					{
						$runden = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
						$runden = $runden["Runde"]-1;
						$mysqli->query("UPDATE ".table." SET Runde='".$runden."' WHERE Nummer=".$_POST["personnummer"]);
						echo "<script type=\"text/javascript\">
									alert(\"Dem Schüler/ Der Schülerin wurde eine Runde abgezogen.\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
					//fals nicht anwesend
					else
					{
						echo "<script type=\"text/javascript\">
									alert(\"Bitte melden sie den Schüler/ die Schülerin erst an!\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
				}
				//Anmelden
				if(isset($_POST["anmelden"])&&$_POST["Oder"]=="Nummer")
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$anwesend = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
					if ($anwesend != "1")
					{
						$mysqli->query("UPDATE ".table." SET Anwesenheit='1' WHERE Nummer=".$_POST["personnummer"]);
						echo "<script type=\"text/javascript\">
									alert(\"Dem Schüler/ Der Schülerin wurde angemeldet.\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
					//fals nicht anwesend
					else
					{
						echo "<script type=\"text/javascript\">
									alert(\"Der Schüler/ die Schülerin ist bereits angemeldet!\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
				}
				//Klasse Anmelden
				if(isset($_POST["anmelden"])&&$_POST["Oder"]=="Klasse")
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$Ausnahme ="";
					$result = $mysqli->query("SELECT * FROM ".table);
					for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
					for($i = 0; $i < count($sqlSelect); $i++)
	        {
	          if($anwesend != "1")
	          {
	           		$mysqli->query("UPDATE ".table." SET Anwesenheit='1' WHERE Klasse=".$_POST["personnummer"]." AND Nummer='".$sqlSelect[$i]["Nummer"]."'");
	          }
						else
						{
							$Ausnahme=$Ausnahme.",".$sqlSelect[$i]["Nummer"];
						}
	        }
						echo "<script type=\"text/javascript\">
									alert(\"Die Klasse wurde erfolgreich angemeldet außer:\n \"".$Ausnahme.")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
				}
				//Abmelden
				if(isset($_POST["abmelden"])&&$_POST["Oder"]=="Nummer")
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$anwesend = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
					if ($anwesend == "1")
					{
						$mysqli->query("UPDATE ".table." SET Anwesenheit='2' WHERE Nummer=".$_POST["personnummer"]);
						echo "<script type=\"text/javascript\">
									alert(\"Dem Schüler/ Der Schülerin wurde angemeldet.\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
					elseif ($anwesend == "2")
					{
						echo "<script type=\"text/javascript\">
									alert(\"Dem Schüler/ Der Schülerin ist bereits abgemeldet.\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
					//fals nicht anwesend
					else
					{
						echo "<script type=\"text/javascript\">
									alert(\"Bitte melden sie den Schüler/die Schülerin erst an\")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
					}
				}
				//Klasse Abmelden
				if(isset($_POST["abmelden"])&&$_POST["Oder"]=="Klasse")
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK! ABMELDUNG ABGEBROCHEN!!!\n");
						fclose($managerLog);
						exit("<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
							</script>");
					}
					$Ausnahme ="";
					$result = $mysqli->query("SELECT * FROM ".table);
					for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
					for($i = 0; $i < count($sqlSelect); $i++)
					{
						if($anwesend != "2")
						{
								$mysqli->query("UPDATE ".table." SET Anwesenheit='2' WHERE Klasse=".$_POST["personnummer"]." AND Nummer='".$sqlSelect[$i]["Nummer"]."'");
						}
						else
						{
							$Ausnahme=$Ausnahme.",".$sqlSelect[$i]["Nummer"];
						}
					}
						echo "<script type=\"text/javascript\">
									alert(\"Die Klasse wurde erfolgreich abgemeldet außer:\n \"".$Ausnahme.")
									window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
								</script>";
				}
				else
				{
					echo "<script type=\"text/javascript\">
								alert(\"Es ist ein Fehler aufgetreten!\")
								window.setTimeout('location.href=\"".url."/manager.php?part=interface\"', 0);
							</script>";
				}
			}
			//Fals Feld leer
			else
			{
				echo "<script type=\"text/javascript\">
							alert(\"Bitte füllen sie zuerst das Feld aus!\")
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
					$managerLog = fopen("./Logs/Manager.log", "a");
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
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_GET["Nummer"]." registriert\n");
						fclose($managerLog);
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
							$managerLog = fopen("./Logs/Manager.log", "a");
							fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat versucht Nummer ".$_GET["Nummer"]." zu registriert, welches aber existierte\n");
							fclose($managerLog);
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
				$managerLog = fopen("./Logs/Manager.log", "a");
				fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
				fclose($managerLog);
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
					</script>");
			}
			$csv = fopen("./Logs/Export.csv","w+");
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
				if (file_exists("./Logs/Export.zip"))
				{
					unlink("./Logs/Export.zip");
				}
				$zipname= "./Export.zip";
				$zip = new ZipArchive();
				if ($zip->open($zipname, ZipArchive::CREATE)!==TRUE)
				{
					$managerLog = fopen("./Logs/Manager.log", "a");
					fwrite($managerLog, strftime("!![%d.%m.%Y_%H:%M]",time())."    Es gabe einen Fehler beim erstellen der Export.zip\n");
					fclose($managerLog);
				    exit("Es ist ein Fehler beim öffnen der ZIP Datei aufgetreten.\n");
				}
				$InfoTXT = fopen("./Logs/Info.txt", "w+");
				fwrite($InfoTXT, "Erstellt am: ");
				fwrite($InfoTXT, strftime("%d.%m.%Y_%H:%M",time())."\nExport.csv enthält die Datenbank und ist UTF8 kodiert\nManager.log & Client.log enthalten die Logs vom manager Interface und vom der Eingabe GUI\nFalls Logs falsch dargestellt werden einfach in .txt umbenennen");
				fclose($InfoTXT);
					$zip->addFile("./Logs/Export.csv");
					$zip->addFile("./Logs/Client.log");
					$zip->addFile("./Logs/Manager.log");
					$zip->addFile("./Logs/Info.txt");
					$zip->close();
				$managerLog = fopen("./Logs/Manager.log", "a");
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
									<p id=Titel>".spruch."</p>
									<p id=Adminname>Account: ".$_SESSION["username"]."</p>
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
		elseif($_GET["part"]=="mysql"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2&&isset($_SESSION["UStufe"])==true&&$_SESSION["UStufe"]=="-1")
		{
			$mysqli = new mysqli(host,user, password, database);
			if($mysqli->connect_errno)
			{
				$managerLog = fopen("./Logs/Manager.log", "a");
				fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
				fclose($managerLog);
				exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
					</script>");
			}
			$sqlSelect = "";
			$sqlSelect = $mysqli->query($_POST["befehl"]);
			$sqlSelect = $sqlSelect->fetch_assoc();
			$sqlSelect = print_r($sqlSelect,true);
			echo "
			<head>
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
					<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
						<input value=\"Über\" type=\"submit\" id=hbbutt>
					</form>
				</div>
				<div id=content3>
					<p id=Fehlermeldung>Erfolgreich mit der Ausgabe: ".$sqlSelect."</p>
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
