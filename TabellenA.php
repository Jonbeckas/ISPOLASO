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
    $Suchfeld = "";
    if (isset($_POST["Suche"])==true&&$_POST["Suche"]!="")
    {
      $Suchfeld = " AND ".$_POST["Auswahl"]."='".$_POST["Suche"]."'";
    }
    echo "
    <head>
      <title>".name."</title>
      <link rel=\"stylesheet\" href=\"Interface.css\">
    </head>
    <table border=\"1\">";
    echo
    "	<tr id=TabUS>
        <td>Nummer</td>
        <td>Name</td>
        <td>Klasse</td>
        <td>Anwesenheit</td>
        <td>Uhrzeit der letzten Runde</td>
        <td>Ankunftszeit</td>
        <td>Abmeldezeit</td>
        <td>Runde</td>

      </tr>";
      for($i = 0; $i <=maxschueler; $i++)
      {
        if (isset($_POST["GroßKlein"])==true)
        {
          if ($_POST["Auswahl"]=="Nummer")
          {
            if ($_POST["GroßKlein"]=="MAX"&&isset($_POST["GroßKlein"])==true)
            {
              $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MAX(".$_POST["Auswahl"].") FROM ".table.")-".$i.$Suchfeld);
            }
            elseif ($_POST["GroßKlein"]=="MIN"&&isset($_POST["GroßKlein"])==true)
            {
              $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MIN(".$_POST["Auswahl"].") FROM ".table.")+".$i.$Suchfeld);
            }
            else
            {
              $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'".$Suchfeld);
            }
          }
          elseif ($_POST["Auswahl"]=="Runde")
          {
            for ($n=0;$n<=maxschueler;$n++)
            {
              if ($_POST["GroßKlein"]=="MAX"&&isset($_POST["GroßKlein"])==true)
              {
                $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MAX(".$_POST["Auswahl"]."-".$i.") FROM ".table.") AND Nummer='".$n."'".$Suchfeld);
              }
              elseif ($_POST["GroßKlein"]=="MIN"&&isset($_POST["GroßKlein"])==true)
              {
                $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE ".$_POST["Auswahl"]."=(SELECT MIN(".$_POST["Auswahl"]."+".$i.") FROM ".table.") AND Nummer='".$n."'".$Suchfeld);
              }
              else
              {
                $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'".$Suchfeld);
              }
            }
          }
        }
        else
        {
          $sqlSelect = $mysqli->query("SELECT * FROM `".table."` WHERE Nummer='".$i."'".$Suchfeld);
        }
        $sqlSelect=$sqlSelect->fetch_assoc();
        if($sqlSelect["Nummer"]!="")
        {
          if (strpos($sqlSelect["Name"],"MAN")!==false)
          {
            echo "";
          }
          else
          {
              echo
              "	<tr>
                  <td>".$sqlSelect["Nummer"]."</td>
                  <td>".$sqlSelect["Name"]."</td>
                  <td>".$sqlSelect["Klasse"]."</td>
                  <td>".$sqlSelect["Anwesenheit"]."</td>
                  <td>".strftime("%H:%M", $sqlSelect["Uhrzeit"])."</td>
                  <td>".$sqlSelect["Ankunftszeit"]."</td>
                  <td>".$sqlSelect["Vorname"]."</td>
                  <td>".$sqlSelect["Runde"]."</td>
                </tr>";
          }
        }
      }
      echo "</table>";
  /*    echo "<script>
              window.setTimeout('location.href=\"".url."/TabellenA.php\"', 30000);
            </script>";*/
 ?>
