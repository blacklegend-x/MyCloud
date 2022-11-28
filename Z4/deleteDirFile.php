<?php
session_start();

$remove_file_dir = $_GET['remove_file_dir'];

if(is_dir($remove_file_dir)){
	$dir_files = array_filter(glob($remove_file_dir . "*"));
	foreach ($dir_files as $files) {
		unlink($files);
		$link = mysqli_connect('mariadb106.server701675.nazwa.pl', 'server701675_bargra1', 'N8CrQi!qb@y3YT@', 'server701675_bargra1'); //polaczenie z baza danych
		if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
		$query = "DELETE FROM file WHERE file_path = '$files'";
		mysqli_query($link, $query);
		mysqli_close($link);
	}
	rmdir($remove_file_dir); //usuniecie katalogu
}
else{
	unlink($remove_file_dir);
	$link = mysqli_connect('mariadb106.server701675.nazwa.pl', 'server701675_bargra1', 'N8CrQi!qb@y3YT@', 'server701675_bargra1'); //polaczenie z baza danych
	if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD
	$query = "DELETE FROM file WHERE file_path = '$remove_file_dir'";
	mysqli_query($link, $query);
	mysqli_close($link);
}

header ('Location: zalogowania.php');
?>