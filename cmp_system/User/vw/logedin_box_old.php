   <div>
        <a href="#"> eng. </a>
        <a href="#"> рус. </a>
        <a href="#" class="colorRed"> укр. </a>
        <br/><br/>
        <span><?php echo $this->EmailPasAuth->userData['email'];?></span>
        <button class="input3 inputPlus" onclick ="
		document.cookie='PHPSESSID=; expires='+(new Date(0)).toGMTString()+'; path=/';
		document.cookie='cmsid=; expires='+(new Date(0)).toGMTString()+'; path=/';
		location.reload();
		return false;">вихід</button>
    </div>
    <br /> 
    <a href="<?php echo $this->url('User', 'ChangeEditMode');?>" class="redac">
		<img alt="nsau" src="lib/img/icon/round.png"/>
		<span class="colorRed"><?php echo ($this->User->getMode()=="edit")? 'Режим перегляду':'Режим редагування';?></span>
	</a><br />
	<a href="<?php echo $this->url('User', 'getRole');?>" class="redac"> Узнать роль </a>
                            