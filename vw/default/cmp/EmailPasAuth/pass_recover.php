<div>
	
	<?php if ($status == 1) {
		echo "Такий email не знайдено";
	} ?>
	<?php if ($status == 2) {
		echo "На вашу пошту вiдправленi iнструкцii по змiнi паролю";
	} ?>
	<br>
	<?php if ($status != 2) { ?>
	Введiть вашу поштову адресу <br>
	<form action="EmailPasAuth/PasswordRecover" method="post">
		<input type="email" name="email">
		<input type="submit" value="Вiдправити">
	</form>
	<?php } ?>
</div>