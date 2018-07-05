<?php
	include "functions.php";

?>
<head>
	<title><?php echo name; ?></title>
</head>
<body>
	<?php

  ?>
    <form action="register.php" method="POST">
        <p>Name:</p>
        <input name="newusername" type="text">
        <p>Bitte geben sie ein Passwort ein:</p>
        <input name="newuserpassword" type="password"><br>
        <p>Bitte bestätigen sie das Passwort:</p>
        <input name="newuserpassword2" type="password"><br>
        <input value="Registrieren" type="submit">
     </form>
</body>
