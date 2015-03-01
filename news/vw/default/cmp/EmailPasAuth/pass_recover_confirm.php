<div>
<?php if ($status === 1 ) { ?>
	<h3>Введiть новий пароль</h3>
	<form action="EmailPasAuth/SetNewPass" method="post">
		<input type="hidden" name="key" value="<?php echo $key ?>">
		<input type="hidden" name="id" value="<?php echo $id ?>">
		<input type="text" placeholder="Введiть новий пароль" name="new_pass">
		<input type="submit">
	</form>
<?php } else { ?>
    Недiйсне посилання
<?php } ?>
</div>


