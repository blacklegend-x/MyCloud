<?php declare(strict_types=1); // włączenie typowania zmiennych
session_start();
if (!isset($_SESSION['loggedin']))
{
	header('Location: login.php');
	exit();
}
?>

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="pl" lang="pl">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="fonts/fontawesome/css/all.css">
<style>
	::-webkit-file-upload-button{
		display: none;
	}
</style>
</head>
<body>
	Zalogowano w aplikacji jako użytkownik: 
	
<?php
$przegladarka = $_SERVER['HTTP_USER_AGENT']; //pobranie informacji o przegladarce goscia strony
$nazwa_przegladarki = wytnij_nazwe_przegladarki($przegladarka); //wywolanie funkcji do pobierania nazwy przegladarki

//przegladarka nazwa
function wytnij_nazwe_przegladarki($user_agent)
{
    if (strpos($user_agent, 'Opera') || strpos($user_agent, 'OPR/')) return 'Opera';
    elseif (strpos($user_agent, 'Edg')) return 'Edge';
    elseif (strpos($user_agent, 'Chrome')) return 'Chrome';
    elseif (strpos($user_agent, 'Safari')) return 'Safari';
    elseif (strpos($user_agent, 'Firefox')) return 'Firefox';
    elseif (strpos($user_agent, 'MSIE') || strpos($user_agent, 'Trident/7')) return 'Internet Explorer';
   
    return 'Other'; //nieznana
}
//pobieranie danych o przegladarce goscia w czasie rzeczywistym (aktualizacja przez odswiezenie strony)
$screen_width_height = "<script>document.write(screen.width);</script>".'x'."<script>document.write(screen.height);</script>";
$browser_width_height = "<script>document.write(window.innerWidth);</script>".'x'."<script>document.write(window.innerHeight);</script>";
$screen_colors = "<script>document.write(screen.colorDepth);</script>";
$cookies_enabled = "<script>document.write(navigator.cookieEnabled);</script>";
$java_enabled = "<script>document.write(navigator.javaEnabled());</script>";
$browser_language = "<script>document.write(navigator.language);</script>";

date_default_timezone_set('Europe/Warsaw');

echo $_SESSION['username']; //wyswietlenie nazwy aktualnie zalogowanego uzytkownika

$ipaddress = $_SERVER["REMOTE_ADDR"]; //ip goscia
function ip_details($ip) { //funkcja do wyodrebniania szczegolow na podstawie ip
	$json = file_get_contents ("http://ipinfo.io/{$ip}/geo");
	$details = json_decode ($json);
	return $details;
}

$details = ip_details($ipaddress); //szczegoly wyodrebnione z adresu ip
$loc = $details -> loc; //aktualna lokalizacja goscia
$dateTime= date('Y-m-d H:i:s');

$link = mysqli_connect('mariadb106.server701675.nazwa.pl', 'server701675_bargra1', 'N8CrQi!qb@y3YT@', 'server701675_bargra1'); //polaczenie z baza danych
if(!$link) { echo"Błąd: ". mysqli_connect_errno()." ".mysqli_connect_error(); } //obsługa błędu połączenia z BD

$username =  $_SESSION['username'];
$result = mysqli_query($link, "SELECT * FROM goscieportalu WHERE username='$username'"); //wiersze, w którym login=login z formularza
$rekord = mysqli_fetch_array($result); //wiersza z BD, struktura zmiennej jak w BD

echo "<br/ >Bieżące dane z sesji:<br /> ";
echo "<table border='1'>
<tr>
<th>ipaddress</th>
<th>datetime</th>
<th>country</th>
<th>city</th>
<th>link</th>
<th>przegladarka</th>
<th>screen resolution</th>
<th>browser resolution</th>
<th>colors</th>
<th>cookies enabled</th>
<th>java enabled</th>
<th>language</th>
</tr>";

// echo $details -> country;
// foreach ($result as $row) {
	echo "<tr>";
	echo "<td>" .$ipaddress."</td>";
	echo "<td>" .$dateTime."</td>";
	echo "<td>" .$details -> country."</td>";
	echo "<td>" .$details -> city."</td>";
	echo "<td>"."<a href='https://www.google.pl/maps/place/$loc'>LINK</a>"."</td>";
	echo "<td>" .wytnij_nazwe_przegladarki($przegladarka)."</td>";
	echo "<td>" .$screen_width_height."</td>";
	echo "<td>" .$browser_width_height."</td>";
	echo "<td>" .$screen_colors."</td>";
	echo "<td>" .$cookies_enabled."</td>";
	echo "<td>" .$java_enabled."</td>";
	echo "<td>" .$browser_language."</td>";
	// }
echo "</table>";

echo "<br><a href='index.php'>Powrót do index.php</a><br/>";
//echo "<br><a href='komunikator.php'>Komunikator</a><br/>";

//wyswietlene wszystkich poprzednich zalogowan uzytkownika:
echo "<br />Poprzednie zalogowania tego użytkownika:<br />";
		
		echo "<table border='1'>
		<tr>
		<th>ipaddress</th>
		<th>datetime</th>
		<th>przegladarka</th>
		</tr>";
		
		foreach ($result as $row) {
			echo "<tr>";
			echo "<td>" .$row['ipaddress']."</td>";
			echo "<td>" .$row['datetime']."</td>";
			echo "<td>" .$row['przegladarka']."</td>";
		}
	
		echo "</table><br />";


//wyswietlanie katalogow
$user_dir = $username . '/'; //katalog macierzysty

if(!isset($_SESSION['current_location'])){ //ustawianie lokalizacji po pierwszym wejsciu na stronie
	$_SESSION['current_location'] = $user_dir; //poczatkowo jestesmy w katalogu macierzystym
}

$files = array_filter(glob($_SESSION['current_location'] . "*")); //wszystkie elementy wewnatrz katalogu
echo "Aktualny folder: " . $_SESSION['current_location'] . "<br>";

if($_SESSION['current_location'] != $user_dir){ //jesli nie jestem w katalogu macierzystym
	echo "<a href='zmien_lokacje.php?go_back=true'><i class='fa-solid fa-arrow-turn-up' style='font-size:24px;'></i></a><br>";
}

foreach($files as $file){
	if(is_dir($file)){ //plik jest katalogiem
		echo "<a href='zmien_lokacje.php?new_location=$file/'>" . $file . "</a>";
		echo "&nbsp&nbsp;<a href='deleteDirFile.php?remove_file_dir=$file/'><i class='fa-regular fa-trash-can' style='font-size:24px;'></i></a><br>"; //ikonka kosza
	}
	else{ //pliki
		$file_ext = substr($file, strpos($file, '.')+1);
		echo "<a href=$file download>" . $file . "</a>";
		if($file_ext == "jpg" || $file_ext == "jpeg" || $file_ext == "png"){
			echo "&nbsp&nbsp;<a href=$file><img src=$file style='width:60px;height:40px'></i></a>"; //ikonka pliku
		}
		else if($file_ext == "mp3"){
			echo "&nbsp&nbsp;<a href=$file><i class='fa-regular fa-file-audio' style='font-size:24px;'></i></a>"; //ikonka pliku
		}
		else if($file_ext == "mp4"){
			echo "&nbsp&nbsp;<a href=$file><i class='fa-regular fa-file-video' style='font-size:24px;'></i></a>"; //ikonka pliku
		}
		echo "&nbsp&nbsp;<a href='deleteDirFile.php?remove_file_dir=$file'><i class='fa-regular fa-trash-can' style='font-size:24px;'></i></a><br>"; //ikonka kosza
	}
}

//dodwanie katalogow

if ($_SESSION['current_location']==$user_dir) {
	echo "<br><form action='nowy_folder.php' method='post'>
		<button type='submit' name='nowy_folder' style='border:none;background-color:#ffffff;font-size:24px;'><i class='fa-solid fa-folder-plus'></i></button>
	  </form></br>";

if(isset($_POST['nazwa_folderu'])){
	$name = $_POST['nazwa_folderu'];
	if(!file_exists($user_dir . $name . '/')){
		mkdir($user_dir . $name . '/', 0777, true);
	}
}
}

?>
<br>
<label for="file">
	<i class="fa-solid fa-cloud-arrow-up" style="font-size:24px;"></i>
</label>

<form method="POST" action="addfiledir.php" enctype="multipart/form-data"><br>

	<!-- <i class="fa-solid fa-cloud-arrow-up"></i> -->
	<input type="file" name="file" id="file" >
	<br>

<!-- File to send:<input type = "file" name = "file" id = "file"> -->
<br>
<input type="submit" value="Send"/>
</form>

<?php
//function change_location($new_location){
//	$_SESSION['current_location'] = $new_location;
//	header("Location: zalogowania.php");
//}


//wlamania:
$result1 = mysqli_query($link, "SELECT * FROM break_ins ORDER BY datetime DESC LIMIT 1"); //wiersze, w którym login=login z formularza
$rekord1 = mysqli_fetch_array($result1); //wiersza z BD, struktura zmiennej jak w 

foreach($result1 as $row){
	echo " <p style= 'color:red';>Ostatnie błędne zalogowanie ".$row['datetime']. " Adres IP: ". $row['ip']. "</p>";
}


mysqli_close($link);
?>

<a href ="logout.php">Wyloguj</a><br/>
</body>
</html>