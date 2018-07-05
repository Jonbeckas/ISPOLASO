<?php
	include "settings.php";
?>
<head>
    <link type="text/css" rel="stylesheet" href="style.css">
	<title><?php echo name ?></title>
</head>
<body>
    <h1>
        <?php echo name ?>
    </h1>
	<form action="client-code.php" method="POST">
		<input class="fill" type="text" name="nummer"><br>
		<input class="button" type="submit" value="Ok">
	</form>
</body>
