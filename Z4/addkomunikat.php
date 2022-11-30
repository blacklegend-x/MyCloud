<?php
session_start();
$time = date('H:i:s', time());
$user = $_SESSION['username'];
$post = $_POST['post'];
$recipient = $_POST['recipient'];

if (IsSet($_POST['post']))
{
	$dbhost=""; $dbuser=""; $dbpassword=""; $dbname="";
	$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
	if (!$connection)
	{
		echo " MySQL Connection error." . PHP_EOL;
		echo "Errno: " . mysqli_connect_errno() . PHP_EOL;
		echo "Error: " . mysqli_connect_error() . PHP_EOL;
		exit;
	}

	$target_dir = $user.'/';
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

	$result = mysqli_query($connection, "INSERT INTO messages (message, user, file, ext, recipient) VALUES ('$post', '$user', '$target_location', '$file_extension', '$recipient');") or die ("DB error: $dbname");
	mysqli_close($connection);
}
header ('Location: komunikator.php');
?>
