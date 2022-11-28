<?php
session_start();

if (file_exists($_FILES["file"]["tmp_name"]))
{
	$dbhost="mariadb106.server701675.nazwa.pl"; $dbuser="server701675_bargra1"; $dbpassword="N8CrQi!qb@y3YT@"; $dbname="server701675_bargra1";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
	if (!$connection)
	{
		echo " MySQL Connection error." . PHP_EOL;
		echo "Errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}

	$target_dir = $_SESSION['current_location'];
	$file_name = $_FILES["file"]["name"];

	//przetwarzanie plikow
	if($file_name != ""){ //jezeli podano nazwe nowego obrazu
		$file_extension = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); //rozszerzenie dodawanego pliku
		$target_location = $target_dir . $file_name; //lokacja docelowa nowego pliku
		move_uploaded_file($_FILES["file"]["tmp_name"], $target_location); //przeniesienie nowego pliku
	}else{
		$file_extension = "";
		$target_location = ""; //jezeli nie podano nowego pliku, to pusta lokalizacja
	}

	$result = mysqli_query($connection, "INSERT INTO file (file_name, file_ext, file_path) VALUES ('$file_name', '$file_extension', '$target_location');") or die ("DB error: $dbname");
	mysqli_close($connection);
}
header ('Location: zalogowania.php');
?>