<div class="container">
	<div class="login-form">
		<?php if ($status == 1) {
			echo "Такой email не найден";
		} ?>
		<?php if ($status == 2) {
			echo "На вашу почту отправлены инструкции по смене пароля";
		} ?>
		<br>
		<?php if ($status != 2) { ?>
		 <br>
		<form id="login" action="EmailPasAuth/loginProcess/login" method="post">
			<fieldset>
				<legend>Введите ваш email</legend>
				<label for="email">Email</label>
				<input class="form-control" id="username" type="email" name="email" placeholder="Email" autofocus required>
				<br />
				<br />
				<input class="btn btn-default" type="submit" id="submit" value="Отправить">
			</fieldset>
		</form>
		<?php } ?>

	</div>
</div>