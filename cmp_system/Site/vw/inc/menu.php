<?php
$events = $this->getEvents();
?>
<div class="menu">
<!-- 
Я засунул бизнес-логику админки в отдельный файл и намешал PHP и HTML.
Извините меня :'(
 -->
		<?php if (($this->User->getRole()=="administrator" or true) /*or ($this->User->getRole()!="unregistered" and $this->is_edit)*/) { ?>
					<a class="pull-left" href="<?php echo $this->url('User', 'ChangeEditMode');?>">
						<?php echo ($this->User->getMode()=="edit")? '<i class="icon-eye-open"></i>':'<i class="icon-edit"></i>';?>
					</a>
		<?php } ?>
	<span class="title">
		<?php echo $this->title;?>
	</span>

<?php
	$this->Menu->e_ShowMenu('site_menu');
?>

<div class="cms-menu">
  <ul class="menu">
	  <li><a href="#">Имя <i class="icon-off"></i></a>
			<ul>
				<li><a href="#">Персональная страница</a></li>
				<li><a href="User/SecurityCancel">Охрана отмена</a></li>
				<li class="divider"></li>
				<li><a href="#">Текст</a></li>
				<li><a href="#">Текст</a></li>
				<li><a href="#">Текст</a></li>
			</ul>
		</li>
	  <li><a href="#">20.47</a>
		
			<ul>
				<li><a href="#">Календарь</a></li>
				<li><a href="#">20.47</a></li>
				<li><a href="#">Текст</a></li>
				<li><a href="#">Текст</a></li>
				<li><a href="#">Текст</a></li>
			</ul>
		</li>
	  <li><a href="#"><i class="icon-wrench"></i></a>
			<ul>
				<li><a href="Роли">Роли <span class="muted"></span></a></li>
				<li><a href="Типы-страниц">Типы страниц <span class="muted"></span></a></li>
				<li><a href="Права-компонентов">Права компонентов <span class="muted"></span></a></li>
				<li><a href="Права-видов">Права видов <span class="muted"></span></a></li>
				<li class="divider"></li>
				<li><a href="Типы-фотографий">Типы фотографий <span class="muted"></span></a></li>
			</ul>
		</li>
		<?php if ($events['count']['events']){?>
		<li><a href="Сообщения-системы"><i class="icon-info-sign"> </i><div class="badge"><?php echo $events['count']['events'];?></div></a>
			<ul>
				<li><a href="Сообщения-системы/debug"   > <span class="muted"><?php echo $events['count']['debug']   ;?></span>Отладка                 </a></li>
				<li><a href="Сообщения-системы/event"   > <span class="muted"><?php echo $events['count']['event']   ;?></span>События                 </a></li>
				<li><a href="Сообщения-системы/info"    > <span class="muted"><?php echo $events['count']['info']    ;?></span>Информация              </a></li>
				<li><a href="Сообщения-системы/warning" > <span class="muted"><?php echo $events['count']['warning'] ;?></span>Предупреджения          </a></li>
				<li><a href="Сообщения-системы/error"   > <span class="muted"><?php echo $events['count']['error']   ;?></span>Ошибки                  </a></li>
				<li><a href="Сообщения-системы/security"> <span class="muted"><?php echo $events['count']['security'];?></span>Безопастность           </a></li>
			</ul>
		</li>
		<?php } ?>
	  <li><a href="#"><i class="icon-bug"></i><div class="badge">55</div></a></li>
	  <li><a href="#">Текст</a></li>

	  <li></li>
  </ul>
</div>
</div>
