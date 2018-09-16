<?php
	//Innitialisierung
	include "settings.php";
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
	//Wenn Seite ohne Parameter besucht wird
	if (isset($_GET["part"])!="input")
	{
		echo "<script type=\"text/javascript\">
					window.setTimeout('location.href=\"client.php?part=input\"', 0);
				</script>";
	}
	//Eingabefeld
	elseif ($_GET["part"]=="input")
	{
		echo
		"<head>
		    <link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\">
				<link href=\"images/icon.png\" type=\"image/png\" rel=\"icon\">
			<title>".name."</title>
		</head>
		<body>
		    <h1>
		        ".name."
		    </h1>
			<form action=\"client.php?part=process\" method=\"POST\">
				<input class=\"fill\" type=\"text\" name=\"data\"><br>
				<input class=\"button\" type=\"submit\" value=\"Ok\">
			</form>
		</body>";
	}
	//Verarbeitung
	elseif($_GET["part"]=="process")
	{
		echo "<head>
			<title>".name."</title>
		</head>";
		$mysqli = new mysqli(host,user, password, database);
		if($mysqli->connect_errno)
		{
			$clientLog = fopen("./Logs/Client.log", "a");
			fwrite($clientLog, strftime("!!![%d.%m.%Y_%H:%M]",time())."    FEHLER BEIN ZUGRIFF AUF DIE DATENBANK!!!\n");
			fclose($clientLog);
			header("Custom-Title: FEHLER 403");
			header("Custom-Message: Es liegt ein Fehler mit der Datenbank vor. Keine Verbindung möglich;");
    		exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \")
					</script>");
		}
		date_default_timezone_set("Europe/Berlin");
		$student = $_POST["data"];
		if (is_numeric($student)==false)
		{
			$clientLog = fopen("./Logs/Client.log", "a");
			fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    ".$student." war keine richtige Zahl\n");
			fclose($clientLog);
			header("Custom-Title: FEHLER 418");
			header("Custom-Message: Keine Gültige Zahl!");
			exit("<script type=\"text/javascript\">
					alert(\"Bitte gebe eine gültige Zahl an.\");
					window.setTimeout('location.href=\"".url."/client.php\"', 10);
				</script>");
		}
		$station = $mysqli->query("SELECT Station FROM ".table." WHERE Nummer='".$student."'");
		$station = $station->fetch_assoc();
		$station = $station["Station"];
		$station = intval($station);
		$result = $mysqli->query("SELECT Uhrzeit FROM ".table." WHERE Nummer='".$student."'");
		$result = $result->fetch_assoc();
		$result = $result["Uhrzeit"];
		$result = intval($result);
		$rounds = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer='".$student."'");
		$rounds = $rounds->fetch_assoc();
		$rounds = $rounds["Runde"];
		$rounds = intval($rounds);
		$Anwesenheit = $mysqli->query("SELECT Anwesenheit FROM ".table." WHERE Nummer='".$student."'");
		$Anwesenheit = $Anwesenheit->fetch_assoc();
		$Anwesenheit = $Anwesenheit["Anwesenheit"];
		$Anwesenheit = intval($Anwesenheit);
		$timestamp = time();
		if ($result == 0 && $rounds==1||$Anwesenheit==0)
		{
			$clientLog = fopen("./Logs/Client.log", "a");
			fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    Nummer ".$student." war nicht Angemeldet oder nicht gefunden\n");
			fclose($clientLog);
			header("Custom-Title: FEHLER 404");
			header("Custom-Message: Der Schueler ".$student." wurde nicht Gefunden oder ist nicht Angemeldet!");
			exit("<script type=\"text/javascript\">
					alert(\"Du wurdest nicht in der Datenbank gefunden oder bist nicht Angemeldet. Bitte melde dich beim SV-Stand\")
					window.setTimeout('location.href=\"".url."/client.php\"', 0);
				</script>");
		}
		if ($timestamp>=$result+mintime)
		{
			$Zeit = time()-$result;
			if ($station==0)
			{
				$rounds=$rounds+1;
				$mysqli->query("UPDATE ".table." SET Uhrzeit='".$timestamp."' WHERE Nummer='".$student."'");
				$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' WHERE Nummer='".$student."'");
				if(isset($_GET["station"])&&$_GET["station"]!="")
				{
					$mysqli->query("UPDATE ".table." SET Station='".$_GET["station"]."' WHERE Nummer='".$student."'");
				}
				$clientLog = fopen("./Logs/Client.log", "a");
				fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    Nummer ".$student." hat Runde ".$rounds." gelaufen\n");
				fclose($clientLog);
				header("Custom-Title: Scan gesendet");
				header("Custom-Message: Dies war Runde: ".$rounds." bei Schüler ".$student.". Diese Runde dauerte ".$Zeit." Sekunden.");
				echo "	<head>
					    		<link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\">
									<link href=\"images/icon.png\" type=\"image/png\" rel=\"icon\">
									<title>".name."</title>
								</head>
								<body>
									<h1>
											".name."
									</h1>
									<p>Dies war Runde: ".$rounds."</p>
									<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
									</script>
								</body>";
			}
			elseif ($station!=0&&$station!=$_GET["station"])
			{
				$rounds=$rounds+1;
				$mysqli->query("UPDATE ".table." SET Uhrzeit='".$timestamp."' WHERE Nummer='".$student."'");
				$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' WHERE Nummer='".$student."'");
				if(isset($_GET["station"])&&$_GET["station"]!="")
				{
					$mysqli->query("UPDATE ".table." SET Station='".$_GET["station"]."' WHERE Nummer='".$student."'");
				}
				$clientLog = fopen("./Logs/Client.log", "a");
				fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    Nummer ".$student." hat Runde ".$rounds." gelaufen\n");
				fclose($clientLog);
				header("Custom-Title: Scan gesendet");
				header("Custom-Message: Dies war Runde: ".$rounds." bei Schüler ".$student.". Diese Runde dauerte ".$Zeit." Sekunden.");
				echo "	<head>
									<link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\">
									<link href=\"images/icon.png\" type=\"image/png\" rel=\"icon\">
									<title>".name."</title>
								</head>
								<body>
									<h1>
											".name."
									</h1>
									<p>Dies war Runde: ".$rounds."</p>
									<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
									</script>
								</body>";
			}
			else
			{
				fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    Nummer ".$student." hat zweimal bei Station ".$POST["station"]." abgescannt\n");
				fclose($clientLog);
				header("Custom-Title: Fehler 410");
				header("Custom-Message: Nummer ".$student." hat zweimal bei Station ".$POST["station"]." abgescannt.");
				echo "	<head>
									<link type=\"text/css\" rel=\"stylesheet\" href=\"style.css\">
									<link href=\"images/icon.png\" type=\"image/png\" rel=\"icon\">
									<title>".name."</title>
								</head>
								<body>
									<h1>
											".name."
									</h1>
									<p>Du hast zweimal an der selben Station abgescannt</p>
									<script type=\"text/javascript\">
										window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
									</script>
								</body>";
			}
		}
		else{
			date_default_timezone_set("Europe/Berlin");
			$zuschnell=time()-$result;
			header("Custom-Title: FEHLER 508");
			header("Custom-Message: Der Schueler ".$student." war ".$zuschnell." Sek. zu schnell! Manuelle Eingabe?");
			$clientLog = fopen("./Logs/Client.log", "a");
			fwrite($clientLog, strftime("[%d.%m.%Y_%H:%M]",time())."    Nummer ".$student." war ".$zuschnell." Sek. zu schnell\n");
			fclose($clientLog);
			echo "<p>Du warst auffällig schnell, bitte melde dich am SV Stand.<p>
				<script type=\"text/javascript\">
					window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
				</script>";
		}
	}
