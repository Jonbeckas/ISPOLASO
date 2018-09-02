<?php
  include "settings.php";
  echo password_hash("iamsuperuser",PASSWORD_DEFAULT);
  $y=1;
  for($x=0;$x<=1000;$x++)
  {
    $mysqli = new mysqli(host,user, password, database);
    if($mysqli->connect_errno)
    {
      exit("<script type=\"text/javascript\">
          alert(\"Es ist ein Fehler beim verbinden mit der Datenbank aufgetreten \");
        </script>");
    }
    $mysqli->query("INSERT INTO `".table."` (`Name`, `Vorname`, `Klasse`, `Nummer`, `Anwesenheit`, `Ankunftszeit`, `Uhrzeit`, `Runde`) VALUES ('NAME_".$x."', 0, '".$y."', '".$x."', '1', '', 0, 0)");
    if($x%10==0)
    {
      $y++;
    }
  }
 ?>
