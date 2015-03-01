<form action = "<?php echo $this->url('EmailPasAuth', 'loginProcess'); ?>" method = "post">
		<input type = "text" class = "authorization_field" name="email" value = "Имя пользователя" onFocus = "this.value = ''" />
		<input type = "password" class = "authorization_field" name="password" value = "Пароль" onFocus = "this.value = ''" />
		<input hidden="isAjax" value="1"/>
		<button class="autorization-submit-button" id = "authorization_btn">ВХІД</button>
		<div class = "small_hr_devider"></div>
		<a href = "<?php echo $this->url('EmailPasAuth', 'PasswReminder'); ?>" class = "registration_links">Забули пароль?</a>
		<div class = "small_hr_devider"></div>
		<a href = "<?php echo $this->url('EmailPasAuth', 'Registration'); ?>" class = "registration_links">Реєстрація</a>
</form>
