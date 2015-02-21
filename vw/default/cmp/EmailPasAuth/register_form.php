<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("EmailPasAuth_register_form");'>×</a>
    <h3>Реєстрація нового користувача</h3>
  </div>
  <div class="modal-body">
		<form class="form-horizontal" id="regForm" action="EmailPasAuth/RegisterUser" method="POST">
  <fieldset>
    <div id="legend">
      <legend class="">Усі поля є обов`язковими для заповнення</legend>
    </div>


    <div class="control-group">
      <!-- E-mail -->
      <label class="control-label" for="email">E-mail</label>
      <div class="controls">
        <input type="text" id="email" name="email" placeholder="username@gmail.com" class="input-xlarge">
        <p class="help-inline">Буде використовуватися як логін при авторизації</p>
      </div>
    </div>

    <div class="control-group">
      <!-- Password-->
      <label class="control-label" for="password">Пароль</label>
      <div class="controls">
        <input type="password" id="password" name="password" placeholder="secret" class="input-xlarge">
      </div>
    </div>

		 <div class="control-group">
      <label class="control-label"  for="surname">Прізвище</label>
      <div class="controls">
        <input type="text" id="surname" name="surname" placeholder="Шевченко" class="input-xlarge">
      </div>
    </div>

	 <div class="control-group">
      <!-- Username -->
      <label class="control-label"  for="name">Ім'я</label>
      <div class="controls">
        <input type="text" id="name" name="name" placeholder="Тарас" class="input-xlarge">
      </div>
    </div>


 	<?php
/* 		 <div class="control-group">
      <label class="control-label"  for="patronymic">По батькові</label>
      <div class="controls">
        <input type="text" id="patronymic" name="patronymic" placeholder="Григорович" class="input-xlarge">
      </div>
    </div> */
	?>

    <div class="control-group">
      <label class="control-label"  for="date">Дата народження</label>
      <div class="controls">
        <input type="date" id="date" name="birthday" placeholder="1990-01-01" class="input-xlarge">
        <input type="hidden"  name="host" value="it-university.com.ua">
      </div>
    </div>

	    <div class="control-group">
      <label class="control-label"  for="phone">Телефон</label>
      <div class="controls">
        <input type="text" id="phone" name="phone" placeholder="+380963680330" class="input-xlarge">
        <p class="help-inline">У форматі +380XX1002030</p>
      </div>
    </div>

  </fieldset>
</form>









  </div>

   <div class="modal-footer">
   <div class="pull-left" style="text-align:left"><small>
		Реєструючись Ви погоджуєтеся з <a href="#">правилами IT-University</a> <br/>
		<span class=" alert-error">
		 Увага! Якщо Ви зареєстровані на порталі ІТ-Арена,<br/> додатково реєструватися непотрібно
		</span>
		</small>
   </div>

			<button class="btn btn-large pull-right btn-success completed" onclick="$('#regForm').submit();">Зареєструватись!</button>
  </div>
</div>
