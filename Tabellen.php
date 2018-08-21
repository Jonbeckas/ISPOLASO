<?php
  	include "settings.php";
  //  if ($_GET["part"]=="Vermisst"&&isset("Vermisst"))
    //{
      $mysqli = new mysqli(host,user, password, database);
      if($mysqli->connect_errno)
      {
        exit("<script type=\"text/javascript\">
            alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
          </script>");
      }
      echo "
      <head>
        <title>".name."</title>
        <link rel=\"stylesheet\" href=\"Interface.css\">
      </head>
      <table border=\"1\">";
      echo
      "	<tr id=TabUS >
          <td>Nr.</td>
          <td>Name</td>
          <td>Klasse</td>
          <td>Anwesenheit</td>
          <td>Uhrzeit ".strftime("%H:%M", time())."</td>
          <td>Ankunftszeit</td>

        </tr>";
        for($i = time()-18900; $i <= time()-2700; $i++)
        {
          $sqlSelect = $mysqli->query("SELECT * FROM `schueler` WHERE Uhrzeit='".$i."' AND Anwesenheit='1'");
          $sqlSelect=$sqlSelect->fetch_assoc();
          if($sqlSelect["Nummer"]!="")
          {
            echo
            "	<tr>
                <td>".$sqlSelect["Nummer"]."</td>
                <td>".$sqlSelect["Name"]."</td>
                <td>".$sqlSelect["Klasse"]."</td>
                <td>".$sqlSelect["Anwesenheit"]."</td>
                <td>".round((time()-$sqlSelect["Uhrzeit"])/(60),0)." Min.</td>
                <td>".$sqlSelect["Ankunftszeit"]."</td>
              </tr>";
          }
        }
        echo "</table>";
        echo "<script>
                window.setTimeout('location.href=\"".url."/Tabellen.php\"', 3000);
              </script>";
      //}
