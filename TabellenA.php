<?php
    include "settings.php";
    print_r($_POST);
    echo "<form action=\"TabellenA.php\" method=\"POST\">
            <select id=\"Auswahl\" name=\"Auswahl\">
              <option value=\"Runde\">Rundenanzahl</option>
              <option value=\"Anwesenheit\">Anwesenheit</option>
              <option value=\"Klasse\">Klasse</option>
              <option value=\"Nummer\">Nummer</option>
            </select>
            <select id=\"GroßKlein\" name=\"GroßKlein\">
              <option value=\"MAX\">Groß->Klein</option>
              <option value=\"MIN\">Klein->Groß</option>
            </select>
            <input name=\"Suche\" type=\"text\">
            <input value=\"OK\" type=\"submit\">
              ";
    $mysqli = new mysqli(host,user, password, database);
    if($mysqli->connect_errno)
    {
      exit("<script type=\"text/javascript\">
          alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
        </script>");
    }
    $Suchfeld = "";
    if (isset($_POST["Suche"])==true&&$_POST["Suche"]!="")
    {
      $Suchfeld = " AND ".$_POST["Auswahl"]."='".$_POST["Suche"]."'";
    }
    echo "<table border=\"1\">";
    echo
    "	<tr>
        <td>Nummer</td>
        <td>Name</td>
        <td>Klasse</td>
        <td>Anwesenheit</td>
        <td>Uhrzeit der letzten Runde</td>
        <td>Ankunftszeit</td>

      </tr>";
      for($i = 0; $i <=maxschueler; $i++)
      {
        if (isset($_POST["GroßKlein"])==true)
        {
          if ($_POST["GroßKlein"]=="MAX"&&isset($_POST["GroßKlein"])==true)
          {
            $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MAX(".$_POST["Auswahl"].") FROM ".table.")-".$i.$Suchfeld);
          }
          elseif ($_POST["GroßKlein"]=="MIN"&&isset($_POST["GroßKlein"])==true)
          {
            $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MIN(".$_POST["Auswahl"].") FROM ".table.")-".$i.$Suchfeld);
          }
          else
          {
            $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'".$Suchfeld);
          }
        }
        else
        {
          $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'".$Suchfeld);
        }
        $sqlSelect=$sqlSelect->fetch_assoc();
        if($sqlSelect["Nummer"]!="")
        {
          echo
          "	<tr>
              <td>".$sqlSelect["Nummer"]."</td>
              <td>".$sqlSelect["Name"]."</td>
              <td>".$sqlSelect["Klasse"]."</td>
              <td>".$sqlSelect["Anwesenheit"]."</td>
              <td>".strftime("%H:%M", $sqlSelect["Uhrzeit"])."</td>
              <td>".$sqlSelect["Ankunftszeit"]."</td>
            </tr>";
        }
      }
      echo "</table>";
 ?>
