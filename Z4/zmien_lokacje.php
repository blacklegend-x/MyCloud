<?php
	session_start();

	if(isset($_GET['go_back'])){
		$_SESSION['current_location'] = dirname($_SESSION['current_location']) . '/'; //cofniecie sie w drzewku katalogow
	}
	else{
		$_SESSION['current_location'] = $_GET['new_location'];
	}
	header("Location: zalogowania.php");
?>