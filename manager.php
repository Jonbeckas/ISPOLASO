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
	function shutDownFunction()
	{
		$error = error_get_last();
		if ($error['type'] === E_ERROR)
		{
			die( "<head>
							<title>".name."</title>
							<link rel=\"stylesheet\" href=\"Interface.css\">
						</head>
						<body>
							<div id=header>
								<p id=Titel>".spruch."</p>
								<p id=Adminname>Account: ".$_SESSION["username"]."</p>
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
								<p id=Fehlermeldung>Es ist ein Fehler aufgetreten, die Aktion wurde nicht ausgeführt.</p>
								<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
									<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
								</form>
							</div>
						</body>");
			}
	}
	register_shutdown_function('shutDownFunction');
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
					if ($Sicherheit=="-1") $Anwesend=0;
					if ($Anwesend==0)
					{
						$dbpasswd = $mysqli->query("SELECT Vorname FROM ".table." WHERE Name='MAN_".$username."'");
						$dbpasswd = $dbpasswd->fetch_assoc();
						$dbpasswd = $dbpasswd["Vorname"];
							if(password_verify($userpassword,$dbpasswd))
							{
								if ($Sicherheit!="-1")
								{
									$mysqli->query("UPDATE ".table." SET Anwesenheit=1 WHERE Name='MAN_".$username."'");
								}
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
																<option value=\"Nummer\">Schüler/-in</option>
																<option value=\"Klasse\">Klasse</option>
															</select>
															<input name=\"anmelden\" value=\"Anmelden\" type=\"submit\" id=button>
															<input name=\"abmelden\" value=\"Abmelden\" type=\"submit\" id=button2>
															<input name=\"p1\" value=\"Runde +1\" type=\"submit\" id=button>
															<input name=\"m1\" value=\"Runde -1\" type=\"submit\" id=button2>
															<input name=\"mint\" value=\"Mindestanwesenheit?\" type=\"submit\" id=button>
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
												<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos>
													<input value=\"Admin Registrieren\" type=\"submit\" id=\"hbbutt\">
												</form>
												<form action=\"manager.php?part=reset\" method=\"POST\" id=hbpos>
													<input value=\"Admin Reseten\" type=\"submit\" id=\"hbbutt\">
												</form>
												<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos>
													<input value=\"Admin Registrieren\" type=\"submit\" id=".$_SESSION["Button"].">
												</form>
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
												<iframe src=\"TabellenB.php\" height=\"300px\" id=\"Admin_iframe\"></iframe>
											</div>
										</body>";
						}
					}

					else
					{
						echo " <head>
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
						fwrite($managerLog, strftime("![%d.%m.%Y_%H:%M]",time())."    ".$username."konnte nicht angemeldet werden\n");
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
										<p id=Fehlermeldung>Bitte melden sie sich erst mit dem Account von anderen PCs ab!</p>
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
											<input name=\"mint\" value=\"Mindestanwesenheit?\" type=\"submit\" id=button>
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
								<form action=\"manager.php?part=register\" method=\"POST\" id=hbpos>
									<input value=\"Admin Registrieren\" type=\"submit\" id=\"hbbutt\">
								</form>
								<form action=\"manager.php?part=reset\" method=\"POST\" id=hbpos>
									<input value=\"Admin Reseten\" type=\"submit\" id=\"hbbutt\">
								</form>
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
								<iframe src=\"TabellenB.php\" height=\"300px\" id=\"Admin_iframe\"></iframe>
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
												<p id=Fehlermeldung>Der neue Admin wurde erfolgreich registriert.</p>
												<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
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
									<a id=Fehlermeldung>ISPOLASO Version 1.4</a><br></p>
									<a id=Zentrieren rel=\"license\" href=\"http://creativecommons.org/licenses/by-nc-sa/4.0/\"><img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png\" /></a><br />This work is licensed under a <a rel=\"license\" href=\"http://creativecommons.org/licenses/by-nc-sa/4.0/\">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
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
									<a id=Fehlermeldung>ISPOLASO Version 1.4</a><br></p>
									<a rel=\"license\"  href=\"http://creativecommons.org/licenses/by-nc-sa/4.0/\"><img alt=\"Creative Commons License\" style=\"border-width:0\" src=\"https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png\" /></a><br />This work is licensed under a <a rel=\"license\" href=\"http://creativecommons.org/licenses/by-nc-sa/4.0/\">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
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
				$anwesend = $mysqli->query("SELECT Nummer FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
				if($anwesend["Nummer"]=="")
				{
					exit("<head>
									<title>".name."</title>
									<link rel=\"stylesheet\" href=\"Interface.css\">
								</head>
								<body>
									<div id=header>
										<p id=Titel>".spruch."</p>
										<p id=Adminname>Account: ".$_SESSION["username"]."</p>
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
										<p id=Fehlermeldung>Die Schülernummer ".$_POST["personnummer"]." existiert nicht</p>
										<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>");
				}
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
					if ($anwesend["Anwesenheit"] == "1")
					{
						$runden = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
						$runden = $runden["Runde"]+1;
						$mysqli->query("UPDATE ".table." SET Runde='".$runden."' WHERE Nummer=".$_POST["personnummer"]);
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." wurde eine Runde hinzugefügt\n");
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." wurde eine Runde hinzugefügt.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					//fals nicht anwesend
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Bitte melden sie den Schüler/die Schülerin erst an.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
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
					if ($anwesend["Anwesenheit"] == "1")
					{
						$runden = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer=".$_POST["personnummer"])->fetch_assoc();
						$runden = $runden["Runde"]-1;
						$mysqli->query("UPDATE ".table." SET Runde='".$runden."' WHERE Nummer=".$_POST["personnummer"]);
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." wurde eine Runde abgezogen\n");
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." wurde eine Runde abgezogen.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					//falls nicht anwesend
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Bitte melden sie den Schüler/die Schülerin erst an</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
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
					if ($anwesend["Anwesenheit"] != "1")
					{
						$mysqli->query("UPDATE ".table." SET Anwesenheit='1' WHERE Nummer=".$_POST["personnummer"]);
						$mysqli->query("UPDATE ".table." SET Ankunftszeit='".time()."' WHERE Nummer=".$_POST["personnummer"]);
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." angemeldet\n");
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Der Schüler/die Schülerin Nummer ".$_POST["personnummer"]." wurde angemeldet.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					//falls schon anwesend
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Der Schüler/die Schülerin Nummer ".$_POST["personnummer"]." ist bereits anwesend.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
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
					$result = $mysqli->query("SELECT * FROM ".table." WHERE Klasse='".$_POST["personnummer"]."'");
					for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
					for($i = 0; $i < count($sqlSelect); $i++)
	        {
						$anwesend = $sqlSelect[$i]["Anwesenheit"];
	          if($anwesend != "1"&& $sqlSelect[$i]["Nummer"]!="0")
	          {
	           		$mysqli->query("UPDATE ".table." SET Anwesenheit='1' WHERE Klasse=".$_POST["personnummer"]." AND Nummer='".$sqlSelect[$i]["Nummer"]."'");
								$mysqli->query("UPDATE ".table." SET Ankunftszeit='".time()."' WHERE Nummer=".$_POST["personnummer"]);
	          }
						elseif( $sqlSelect[$i]["Nummer"]=="0")
						{

						}
						else
						{
							$Ausnahme=$Ausnahme.",".$sqlSelect[$i]["Nummer"];
						}
	        }
					$managerLog = fopen("./Logs/Manager.log", "a");
					fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat die Klasse ".$_POST["personnummer"]." angemeldet, außer: ".$Ausnahme."\n");
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
										<a id=TextHB>Nachricht</a>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content3>
										<p id=Fehlermeldung>Die Klasse ".$_POST["personnummer"]." wurde erfolgreich angemeldet außer: ".$Ausnahme."</p>
										<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
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
					if ($anwesend["Anwesenheit"] == "1")
					{
						$mysqli->query("UPDATE ".table." SET Anwesenheit='2' WHERE Nummer=".$_POST["personnummer"]);
						$mysqli->query("UPDATE ".table." SET Vorname='".time()."' WHERE Nummer=".$_POST["personnummer"]);
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat dem Schüler/der Schülerin Nummer ".$_POST["personnummer"]." abgemeldet\n");
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Der Schüler/die Schülerin Nummer ".$_POST["personnummer"]." wurde erfolgreich abgemeldet.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					elseif ($anwesend["Anwesenheit"] == "2")
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Der Schüler/die Schülerin Nummer ".$_POST["personnummer"]." ist bereits abgemeldet.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
					}
					//fals nicht anwesend
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
											<a id=TextHB>Nachricht</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=content3>
											<p id=Fehlermeldung>Bitte melden sie den Schüler/die Schülerin Nummer  ".$_POST["personnummer"]." erst an.</p>
											<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
												<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
											</form>
										</div>
									</body>";
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
					$result = $mysqli->query("SELECT * FROM ".table." WHERE Klasse='".$_POST["personnummer"]."'");
					for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
					for($i = 0; $i < count($sqlSelect); $i++)
					{
						$anwesend = $sqlSelect[$i]["Anwesenheit"];
						if($anwesend != "2"&& $sqlSelect[$i]["Nummer"]!="0")
						{
								$mysqli->query("UPDATE ".table." SET Anwesenheit='2' WHERE Klasse=".$_POST["personnummer"]." AND Nummer='".$sqlSelect[$i]["Nummer"]."'");
								$mysqli->query("UPDATE ".table." SET Vorname='".time()."' WHERE Nummer=".$_POST["personnummer"]);
						}
						elseif( $sqlSelect[$i]["Nummer"]=="0")
						{

						}
						else
						{
							$Ausnahme=$Ausnahme.",".$sqlSelect[$i]["Nummer"];
						}
					}
					$managerLog = fopen("./Logs/Manager.log", "a");
					fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat die Klasse ".$_POST["personnummer"]." abgemeldet, außer: ".$Ausnahme."\n");
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
										<a id=TextHB>Nachricht</a>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content3>
										<p id=Fehlermeldung>Die Klasse ".$_POST["personnummer"]." wurde erfolgreich abgemeldet, außer: ".$Ausnahme.".</p>
										<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
				}
				//Mindestanwesenheit checken
				elseif (isset($_POST["mint"]))
				{
					$mysqli = new mysqli(host,user, password, database);
					if($mysqli->connect_errno)
					{
						$clientLog = fopen("./Logs/Client.log", "a");
						fwrite($clientLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
						fclose($clientLog);
			    		exit("<script type=\"text/javascript\">
									alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \")
								</script>");
					}
					$mint=$mysqli->query("SELECT Ankunftszeit FROM ".table." WHERE Nummer='".$_POST["personnummer"]."'")->fetch_Assoc();
					if((time()-$mint["Ankunftszeit"])>minttime)
					{
						$Mint="hat seine/ihre Pflichtzeit";
					}
					else
					{
						$Mint="hat seine/ihre Pflichtzeit nicht";
					}
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
										<a id=TextHB>Nachricht</a>
										<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
											<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
										</form>
										<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
											<input value=\"Über\" type=\"submit\" id=hbbutt>
										</form>
									</div>
									<div id=content3>
										<p id=Fehlermeldung>Der/die Schüler/-in ".$Mint." gelaufen.</p>
										<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
											<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
										</form>
									</div>
								</body>";
				}
			}
			//Fals Feld leer
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
									<a id=TextHB>Nachricht</a>
									<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
										<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
									</form>
									<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
										<input value=\"Über\" type=\"submit\" id=hbbutt>
									</form>
								</div>
								<div id=content3>
									<p id=Fehlermeldung>Bitte füllen sie zuerst das Feld aus.</p>
									<form action=\"".url."/manager.php?part=interface\" method=\"POST\" >
										<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
									</form>
								</div>
							</body>";
			}
		}
		//SuS Registrieren
		elseif ($_GET["part"]=="anmelden"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2&&$_SESSION["UStufe"]=="1")
		{
			if (isset($_GET["process"])==true)
			{
				if($_POST["Nummer"]=="0"||$_POST["Nummer"]=="") die("<head>
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
									<p id=Fehlermeldung>Die Schüler/innen Nummer darf nicht Null oder leer sein</p>
									<div id=login2>
										 <form action=\"manager.php?part=anmelden\" method=\"POST\" id=hbpos2 >
											<input value=\"Neuen Schüler registrieren\" type=\"submit\" id=button>
										 </form>
										 <form action=\"manager.php?part=interface\" method=\"POST\" id=Rechts>
												<input value=\"Zum Dashboard\" type=\"submit\" id=button>
										</form>
										</div>
								</div>
							</body>");
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
					$result = $mysqli->query("SELECT Nummer FROM ".table." WHERE Nummer='".$_POST["Nummer"]."'");
					$result = $result->fetch_assoc();
					$result = $result["Nummer"];
					$result = intval($result);
					if($result=="")
					{
						$mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Ankunftszeit`, `Uhrzeit`, `Runde`) VALUES ('".$_POST["Name"]."', 0, '".$_POST["Klasse"]."', '".$_POST["Nummer"]."', '', '', 0, 0)");
						$managerLog = fopen("./Logs/Manager.log", "a");
						fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat Nummer ".$_POST["Nummer"]." registriert\n");
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
											<p id=Fehlermeldung>Schüler/innen ".$_POST["Name"]." wurde erfolgreich registriert.</p>
											<div id=login2>
												 <form action=\"manager.php?part=anmelden\" method=\"POST\" id=hbpos2 >
													<input value=\"Neuen Schüler registrieren\" type=\"submit\" id=button>
												 </form>
												 <form action=\"manager.php?part=interface\" method=\"POST\" id=Rechts>
														<input value=\"Zum Dashboard\" type=\"submit\" id=button>
												</form>
												</div>
										</div>
									</body>";
					}
					else
						{
							$managerLog = fopen("./Logs/Manager.log", "a");
							fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat versucht den Schüler/die Schülerin Nummer ".$_POST["Nummer"]." zu registriert, welche/r aber schon existiert\n");
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
												<a id=TextHB>Schüler/in registrieren-Der Schüler/die Schülerin existiert bereit</a>
												<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
													<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
												</form>
												<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
													<input value=\"Über\" type=\"submit\" id=hbbutt>
												</form>
											</div>
											<div id=login2>
												<form action=\"manager.php?part=anmelden&process=on\" method=\"POST\">
													<p id=login>Fülle alle Felder aus und drücke \"Registrieren\":</p>
													<input name=\"Name\" type=\"text\" placeholder=\"Bitte geben sie den Name an:\" id=InputRegi>
													<div id=login2>
														<input name=\"Nummer\" type=\"text\" placeholder=\"Bitte gebe sie die Schüler/Schülerinnen Nummer an:\" id=InputRegi>
													</div>
													<div id=login2>
														<input name=\"Klasse\" type=\"text\" placeholder=\"Bitte geben sie die Klasse an:\" id=InputRegi><br>
													</div>
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
											<a id=TextHB>Schüler/in registrieren</a>
											<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
												<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
											</form>
											<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2>
												<input value=\"Über\" type=\"submit\" id=hbbutt>
											</form>
										</div>
										<div id=login2>
											<form action=\"manager.php?part=anmelden&process=on\" method=\"POST\">
												<p id=login>Fülle alle Felder aus und drücke \"Registrieren\":</p>
												<input name=\"Name\" type=\"text\" placeholder=\"Bitte geben sie den Name an:\" id=InputRegi>
												<div id=login2>
													<input name=\"Nummer\" type=\"text\" placeholder=\"Bitte gebe sie die Schüler/Schülerinnen Nummer an:\" id=InputRegi>
												</div>
												<div id=login2>
													<input name=\"Klasse\" type=\"text\" placeholder=\"Bitte geben sie die Klasse an:\" id=InputRegi><br>
												</div>
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
			$result=$mysqli->query("SELECT * FROM ".table);
			for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
      $sort  = array_column($sqlSelect, 'Nummer');
      array_multisort($sort, SORT_ASC, $sqlSelect);
			for($i = 1; $i <= count($sqlSelect); $i++)
			{
				if($sqlSelect[$i]["Nummer"]!=""&&$sqlSelect[$i]["Nummer"]!="0")
				{
					fwrite($csv,
					$sqlSelect[$i]["Nummer"].","
					.$sqlSelect[$i]["Name"].","
					.$sqlSelect[$i]["Klasse"].","
					.$sqlSelect[$i]["Anwesenheit"].","
					.strftime("%H:%M", $sqlSelect[$i]["Uhrzeit"]).","
					.$sqlSelect[$i]["Ankunftszeit"].","
					.$sqlSelect[$i]["Vorname"].","
					.$sqlSelect[$i]["Runde"].","
					.$sqlSelect[$i]["Station"]."\n");
				}
			}
			fclose($csv);
			if (file_exists("./Logs/Export.zip"))
			{
				unlink("./Logs/Export.zip");
			}
			$zipname= "./Logs/Export.zip";
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
				$zip->addFile("./Logs/Export.csv","Export.csv");
				$zip->addFile("./Logs/Client.log","Client.log");
				$zip->addFile("./Logs/Manager.log","Manager.log");
				$zip->addFile("./Logs/Info.txt","Info.txt");
				$zip->close();
			$managerLog = fopen("./Logs/Manager.log", "a");
			fwrite($managerLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$_SESSION["username"]." hat die Logs&Tabellen exportiert\n");
			fclose($managerLog);
			header("Content-Type: application/zip");
	    header("Content-Disposition: attachment; filename=\"Export_".name."_".strftime("%d.%m.%Y_%H:%M",time()).".zip\"");
	    readfile("./Logs/Export.zip");
		}
		//Admin reseten
		elseif($_GET["part"]=="reset"&&isset($_GET["part"])&&isset($_SESSION["username"])==true&&session_status()==2&&isset($_SESSION["UStufe"])==true&&$_SESSION["UStufe"]=="-1")
		{
			if (isset($_GET["process"]))
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
				$mysqli->query("UPDATE ".table." SET Anwesenheit=0 WHERE Name='MAN_".$_POST["name"]."'");
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
						<p id=Fehlermeldung>Erfolgreich die Anwesenheit des Admins ".$_POST["name"]." resetet</p>
						<form action=\"manager.php?part=interface\" method=\"POST\" >
							<input value=\"Okay\" type=\"submit\" id=Fehlerbutton>
						</form>
					</div>
				</body>";
			}
			else
			{
				echo"
				<head>
					<title>".name."</title>
					<link rel=\"stylesheet\" href=\"Interface.css\">
				</head>
				<body>
					<div id=header>
						<p id=Titel>".spruch." HOHE RECHTE</p>
						<p id=Adminname>Account: ".$_SESSION["username"]."</p>
					</div>
					<div id=hotbar>
						<a id=TextHB>Admins Reseten</a>
						<form action=\"manager.php?part=interface\" method=\"POST\" id=hbpos>
							<input value=\"Zum Dashboard\" type=\"submit\" id=hbbutt>
						</form>
						<form action=\"manager.php?part=logout\" method=\"POST\" id=hbpos2>
							<input value=\"Ausloggen\" type=\"submit\" id=hbbutt>
						</form>
						<form action=\"manager.php?part=about\" method=\"POST\" id=hbpos2 >
							<input value=\"Über\" type=\"submit\" id=hbbutt>
						</form>
					</div>
					<div id=content3>
						<p id=Fehlermeldung>Wie lautet der Name des Admins?</p>
						<form action=\"manager.php?part=reset&process=on\" method=\"POST\" >
							<input name=\"name\" type=\"Text\" placeholder=\"Adminname\" id=InputRegi>
							<input name=\"\" value=\"Okay\" type=\"submit\" id=button>
						</form>
					</div>
				</body>";
			}
		}
		//MYSQL für Admin
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
			$result = $mysqli->query($_POST["befehl"]);
			if($result!==TRUE&&$result!==FALSE)
			{
				for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
				$sort  = array_column($sqlSelect, "Nummer");
      	array_multisort($sort, SORT_ASC, $sqlSelect);
			}
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
				<div id=content3>";

				if ($result!==TRUE&&$result!==FALSE)
				{
					echo"
					<p id=Fehlermeldung>Erfolgreich mit der Ausgabe: </p>
					<table border=\"1\">
						<tr id=TabUS>
				        <td>Nr.</td>
				        <td>Name</td>
				        <td>Klasse</td>
				        <td>Anwesenheit</td>
				        <td>Uhrzeit der <br> letzten Runde</td>
				        <td>Ankunftszeit</td>
				        <td>Abmeldezeit</td>
				        <td>Runde</td>
				        <td>Station</td>
				      </tr>";
							for($i=0;$i<count($sqlSelect);$i++)
							{
								echo
											"	<tr>
													<td>".$sqlSelect[$i]["Nummer"]."</td>
													<td>".$sqlSelect[$i]["Name"]."</td>
													<td>".$sqlSelect[$i]["Klasse"]."</td>
													<td>".$sqlSelect[$i]["Anwesenheit"]."</td>
													<td>".strftime("%H:%M", $sqlSelect[$i]["Uhrzeit"])."</td>
													<td>".$sqlSelect[$i]["Ankunftszeit"]."</td>
													<td>".$sqlSelect[$i]["Vorname"]."</td>
													<td>".$sqlSelect[$i]["Runde"]."</td>
													<td>".$sqlSelect[$i]["Station"]."</td>
												</tr>";
							}
						echo"
						</table>";
				}
				elseif ($result===TRUE)
				{
					echo "<p id=Fehlermeldung>Erfolgreich</p>";
				}
				else
				{
					echo "<p id=Fehlermeldung>Es ist ein Fehler aufgetreten</p>";
				}
				echo
					"<form action=\"manager.php?part=interface\" method=\"POST\" >
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
