<?php
	include "settings.php";
?>
<head>
	<title><?php echo name ?></title>
</head>
<body>
	<form action="client-code.php" method="POST">
		<input type="text" name="nummer"><br>
		<input type="submit" value="Ok">
	</form>
</body>