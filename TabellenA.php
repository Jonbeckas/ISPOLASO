<?php
    include "settings.php";
    session_start();
    if (isset($_SESSION["username"])==false||$_SESSION["username"]=="") die("Zugriff Verweigert. Bitte melden sie sich erst an");
    echo "<form action=\"TabellenA.php\" method=\"POST\">
            <select id=\"Auswahl\" name=\"Auswahl\">
              <option value=\"Runde\">Rundenanzahl</option>
              <option value=\"Anwesenheit\">Anwesenheit</option>
              <option value=\"Klasse\">Klasse</option>
              <option value=\"Nummer\">Nummer</option>
              <option value=\"Name\">Name</option>
              <option value=\"Station\">Station</option>
            </select>
            <select id=\"GroßKlein\" name=\"GroßKlein\">
              <option value=\"SORT_DESC\">Groß->Klein (Absteigend)</option>
              <option value=\"SORT_ASC\">Klein->Groß (Aufsteigend)</option>
            </select>
            <input name=\"Suche\" type=\"text\" id=eingabeDB>
            <input value=\"OK\" type=\"submit\" id=button>
              ";
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
        if (isset($_POST["GroßKlein"])==true&&isset($_POST["Auswahl"])==true)
        {
          $sort  = array_column($sqlSelect, $_POST["Auswahl"]);
          if ($_POST["GroßKlein"] == "SORT_DESC")
          {
            array_multisort($sort, SORT_DESC, $sqlSelect);
          }
          elseif ($_POST["GroßKlein"] == "SORT_ASC")
          {
            array_multisort($sort, SORT_ASC, $sqlSelect);
          }
        }
        else
        {
            $sort  = array_column($sqlSelect, "Nummer");
            array_multisort($sort, SORT_ASC, $sqlSelect);
        }

      for($i=0;$i<count($sqlSelect);$i++)
      {
        if (strstr($sqlSelect[$i]["Name"],"MAN_")===false)
        {
          if (isset($_POST["Suche"])&&$_POST["Suche"]!="")
          {
            if ($sqlSelect[$i][$_POST["Auswahl"]]==$_POST["Suche"])
            {
              if($sqlSelect[$i]["Uhrzeit"]!="0") $Uhrzeit=strftime("%H:%M", $sqlSelect[$i]["Uhrzeit"]);
              else $Uhrzeit="";
              if($sqlSelect[$i]["Ankunftszeit"]!="0") $Ankunft=strftime("%H:%M", $sqlSelect[$i]["Ankunftszeit"]);
              else $Ankunft="";
              if($sqlSelect[$i]["Vorname"]!="0") $Abmeldung=strftime("%H:%M", $sqlSelect[$i]["Vorname"]);
              else $Abmeldung="";
              echo
                    "	<tr>
                        <td>".$sqlSelect[$i]["Nummer"]."</td>
                        <td>".$sqlSelect[$i]["Name"]."</td>
                        <td>".$sqlSelect[$i]["Klasse"]."</td>
                        <td>".$sqlSelect[$i]["aw"]."</td>
                        <td>".$Uhrzeit."</td>
                        <td>".$Ankunft."</td>
                        <td>".$Abmeldung."</td>
                        <td>".$sqlSelect[$i]["Runde"]."</td>
                        <td>".$sqlSelect[$i]["Station"]."</td>
                      </tr>";
            }
          }
          else
          {
            if($sqlSelect[$i]["Uhrzeit"]!="0") $Uhrzeit=strftime("%H:%M", $sqlSelect[$i]["Uhrzeit"]);
            else $Uhrzeit="";
            if($sqlSelect[$i]["Ankunftszeit"]!="0") $Ankunft=strftime("%H:%M", $sqlSelect[$i]["Ankunftszeit"]);
            else $Ankunft="";
            if($sqlSelect[$i]["Vorname"]!="0") $Abmeldung=strftime("%H:%M", $sqlSelect[$i]["Vorname"]);
            else $Abmeldung="";
            echo
                  "	<tr>
                      <td>".$sqlSelect[$i]["Nummer"]."</td>
                      <td>".$sqlSelect[$i]["Name"]."</td>
                      <td>".$sqlSelect[$i]["Klasse"]."</td>
                      <td>".$sqlSelect[$i]["Anwesenheit"]."</td>
                      <td>".$Uhrzeit."</td>
                      <td>".$Ankunft."</td>
                      <td>".$Abmeldung."</td>
                      <td>".$sqlSelect[$i]["Runde"]."</td>
                      <td>".$sqlSelect[$i]["Station"]."</td>
                    </tr>";
          }
        }
      }
      echo "</table>";
      echo "<script>
              window.setTimeout('location.href=\"".url."/TabellenA.php\"', 60000);
            </script>";
 ?>
