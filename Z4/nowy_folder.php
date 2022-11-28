<?php
	session_start();
	echo "Nowy folder<br>
			<form action='zalogowania.php' method='post'>
				Nazwa: <input type='text' name='nazwa_folderu' maxlength='50' size='50'>
				<input type='submit' value='Zatwierdź'>
			</form><br>";
?>