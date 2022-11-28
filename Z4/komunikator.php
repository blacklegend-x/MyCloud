<?php 
session_start();
if(IsSet($_SESSION['username']) == 0){
	die("Brak zalogowanego użytkownika! <a href='index.php'>Powrót do index.php</a><br/>");
}

$dbhost="mariadb106.server701675.nazwa.pl"; $dbuser="server701675_bargra1"; $dbpassword="N8CrQi!qb@y3YT@"; $dbname="server701675_bargra1";
$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if (!$connection)
{
echo " MySQL Connection error." . PHP_EOL;
echo "Errno: " . mysqli_connect_errno() . PHP_EOL;
echo "Error: " . mysqli_connect_error() . PHP_EOL;
exit;
}

$result = mysqli_query($connection, "SELECT * FROM users") or die ("DB error: $dbname");
?>

<form method="POST" action="addkomunikat.php" enctype="multipart/form-data"><br>
<label for="recipient">Recipient:</label>
<select id="recipient" name="recipient">
  <?php 
  while ($row = mysqli_fetch_array ($result))
  {
  $user = $row[1]; //nazwy uzytkownikow
  ?>
    <option value=<?=$user?>><?=$user?></option>
  <?php
  }
  mysqli_close($connection);
  ?>
</select><br>

Post:<input type="text" name="post" maxlength="90" size="90"><br>

File to send:<input type = "file" name = "file" id = "file">

<input type="submit" value="Send"/>
<br><a href="index.php">Powrót do index.php</a><br/>
</form>

<?php
$dbhost="mariadb106.server701675.nazwa.pl"; $dbuser="server701675_bargra"; $dbpassword="N8CrQi!qb@y3YT@"; $dbname="server701675_bargra";
$connection = mysqli_connect($dbhost, $dbuser, $dbpassword, $dbname);
if (!$connection)
{
echo " MySQL Connection error." . PHP_EOL;
echo "Errno: " . mysqli_connect_errno() . PHP_EOL;
echo "Error: " . mysqli_connect_error() . PHP_EOL;
exit;
}


$username = $_SESSION['username'];
echo "Tablica użytkownika $username";

if ($username == 'admin') {
	$result = mysqli_query($connection, "Select * from messages order by idk desc") or die ("DB error: $dbname");
}else{
	$result = mysqli_query($connection, "Select * from messages WHERE user='$username' OR recipient='$username'  order by idk desc") or die ("DB error: $dbname");

}


while ($row = mysqli_fetch_array ($result))
{
	$id = $row[0];
	$date = $row[1];
	$message= $row[2];
	$user = $row[3];
	$file = $row[4];
	$ext = $row[5];
	$recipient = $row[6];
	?>
	<br>Post użytkownika <?=$user?>:<br>

	<?php 
	if($file != "") //jesli plik istnieje
	{ 
		if($ext == "mp4"){
			echo "<video controls autoplay muted width='320px' height='240px'><source src='$file' type='video/mp4'></video><br>";
		}
		if($ext == "mp3"){
			echo "<audio controls><source src='$file' type='audio/mpeg'></audio><br>";
		}
		if($ext == "jpg" || $ext == "png" || $ext == "jpeg" || $ext == "gif"){
			echo "<img src='$file'><br>";
		}
	}
	?>
	<?=$message?><br>
	<?php 
}

mysqli_close($connection);
?>