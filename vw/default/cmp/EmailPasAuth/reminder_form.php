<div class="color">
    <div>
        <a href="#"> eng. </a>
        <a href="#"> рус. </a>
        <a href="#" class="colorRed"> укр. </a>
        <form class="logIn" action="<?php echo $this->url('EmailPasAuth', 'Reminder'); ?>" method="post">
            <input type="text" class="input1" name="email" value="Email користувача"/>
            <br />
            <button class="input3">Надіслати пароль на Email</button>
        </form>
     </div>
</div>