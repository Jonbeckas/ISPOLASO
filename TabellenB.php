<?php
    include "settings.php";
    session_start();
    if (isset($_SESSION["username"])==false||$_SESSION["username"]=="") die("Zugriff Verweigert. Bitte melde die erst an");
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
    "	<tr id=TabUS>
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
      $result = $mysqli->query("SELECT * FROM ".table);
      for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
      $sort  = array_column($sqlSelect, "Nummer");
      array_multisort($sort, SORT_ASC, $sqlSelect);

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
      echo "</table>";
      echo "<script>
              window.setTimeout('location.href=\"".url."/TabellenB.php\"', 60000);
            </script>";
 ?>
