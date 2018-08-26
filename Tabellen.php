<?php
  	include "settings.php";
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
        $result = $mysqli->query("SELECT * FROM ".table);
        for ($sqlSelect = array (); $row = $result->fetch_assoc(); $sqlSelect[] = $row);
        $sort  = array_column($sqlSelect, 'Uhrzeit');
        array_multisort($sort, SORT_DESC, $sqlSelect);
        for($i = 0; $i < count($sqlSelect); $i++)
        {
          if($sqlSelect[$i]["Nummer"]!=""&&$sqlSelect[$i]["Anwesenheit"]=="1"&&strstr($sqlSelect[$i]["Name"],"MAN_")===false&&$sqlSelect[$i]["Uhrzeit"]!=""&&$sqlSelect[$i]["Uhrzeit"]!="0"&&$sqlSelect[$i]["Uhrzeit"] <= time()-2700)
          {
            echo
            "	<tr>
                <td>".$sqlSelect[$i]["Nummer"]."</td>
                <td>".$sqlSelect[$i]["Name"]."</td>
                <td>".$sqlSelect[$i]["Klasse"]."</td>
                <td>".$sqlSelect[$i]["Anwesenheit"]."</td>
                <td>".round((time()-$sqlSelect[$i]["Uhrzeit"])/(60),0)." Min.</td>
                <td>".$sqlSelect[$i]["Ankunftszeit"]."</td>
              </tr>";
          }
        }
        echo "</table>";
        echo "<script>
                window.setTimeout('location.href=\"".url."/Tabellen.php\"', 3000);
              </script>";
?>
