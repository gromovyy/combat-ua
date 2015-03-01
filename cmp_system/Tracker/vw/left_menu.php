<div>
	<h1>
		<a id="logo" href="main">
			<img src="lib/img/logo.jpg" width="60px">
			<span class="g-menu-top">IT-</span><span class="s-menu-top">FACTORY</span>
		</a>
	</h1>
	<nav class="menu nav">
		<ul class="nav nav-pills nav-stacked" id="left-menu">
			<li class="active">
			<a href="#" data-toggle="collapse" data-target="#projects, #people">
				<?php $this->Project->loadToolBox("project",null,null,null,null,array('add'), 'Project/List') ?>
				<i class="glyphicon glyphicon-book"></i>Проекти
			</a>
			<?php $this->Project->e_List($id_project); ?>
			</li>
			<?php if ($this->User->getRealRole() == 'administrator') { ?>
				<li><a href="#" data-toggle="collapse" data-target="#people, #projects"><i class="glyphicon glyphicon-calendar"></i>Співробітники</a>
				<?php $this->Member->e_List(); ?>
				</li>
			<?php  } ?>
		</ul>
	</nav>
</div>