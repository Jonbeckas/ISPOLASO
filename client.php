<?php
	//Innitialisierung
	include "settings.php";
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
			<title>".name."</title>
		</head>
		<body>
		    <h1>
		        ".name."
		    </h1>
			<form action=\"client.php?part=process\" method=\"POST\">
				<input class=\"fill\" type=\"text\" name=\"content\"><br>
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
		if($mysqli->connect_errno) {
    		exit("<script type=\"text/javascript\">
						alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \")
					</script>");
		}
		date_default_timezone_set("Europe/Berlin");
		$student = $_POST["content"];
		if (is_numeric($student)==false)
		{
			exit("<script type=\"text/javascript\">
					alert(\"Bitte gebe eine gültige Zahl an.\");
					window.setTimeout('location.href=\"".url."/client.php\"', 10);
				</script>");
		}
		$result = $mysqli->query("SELECT Uhrzeit FROM ".table." WHERE Nummer='".$student."'");
		$result = $result->fetch_assoc();
		$result = $result["Uhrzeit"];
		$result = intval($result);
		$rounds = $mysqli->query("SELECT Runde FROM ".table." WHERE Nummer='".$student."'");
		$rounds = $rounds->fetch_assoc();
		$rounds = $rounds["Runde"];
		$rounds = intval($rounds);
		$rounds = $rounds+1;
		$timestamp = time();
		if ($result == 0 && $rounds==1){
		exit("<script type=\"text/javascript\">
					alert(\"Du wurdest nicht in der Datenbank gefunden. Bitte melde dich beim SV-Stand\")
					window.setTimeout('location.href=\"".url."/client.php\"', 0);
				</script>");
		}
		if ($timestamp>=$result+mintime){
			$mysqli->query("UPDATE ".table." SET Uhrzeit='".$timestamp."' WHERE Nummer='".$student."'");
			$mysqli->query("UPDATE ".table." SET Runde='".$rounds."' WHERE Nummer='".$student."'");
			echo "<p>Dies war Runde</p><br><p>".$rounds."</p>
					<script type=\"text/javascript\">
						window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
					</script>";
		}
		else{
			echo "<p>Du warst auffällig schnell, bitte melde dich am SV Stand.<p>
				<script type=\"text/javascript\">
					window.setTimeout('location.href=\"".url."/client.php\"', ".countdown.");
				</script>";
		}
	}
