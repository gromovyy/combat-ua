<div class="modal span4">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("EmailPasAuth_login_form");'>×</a>
    <h3>Авторизація</h3>
  </div>
  <div class="modal-body">
  <center>
	<form action = "<?php echo $this->url('EmailPasAuth', 'loginProcess'); ?>" method = "post">
			<input type = "email"  name="email" placeholder = "Ім'я користувача" /><br/>
			<input type = "password"  name="password" placeholder = "Пароль"  /><br/>
			<button class="btn btn-large" type="submit" >ВХІД</button>

	</form>
	</center>
  </div>
</div>






