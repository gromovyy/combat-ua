<?php 	
	$view_update_link = "Menu/ShowMenu";
	$is_update = ($this->User->getRole()=="administrator" and $this->User->getMode()=="edit")?true:false;
?>
<div class="navbar">
	<div class="navbar-inner">
		<div class="container">
		
		<a class="brand" href="<?php echo $baseUrl?>"></a>
		<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">Меню</a>
		<div class="pull-right">
			<?php $this->User->showLoginBox(); ?>			
		</div>
		
		
		<div class="nav-collapse collapse">
		<ul class="nav">
			<?php if (!empty($main_menu)) foreach($main_menu as $menu) { ?>
			<li class="<?php if(!empty($menu['sub_menu'])) echo 'dropdown'; ?>">
				<?php if ($is_update) { 
				$visible = $this->getVisibility($menu["is_visible"]);
						$this->loadToolBox("menu", $menu['id_menu'], 'Menu', 'parent_menu', '0', Array("left", "right","edit", $visible, "delete"), $view_update_link);
				
				
				
				} ?>
				<?php // Дописать 'data-toggle="dropdown"  для фиксирования выпадающего меню ?>
				<a href="<?php echo $menu["url"];?>" <?php if (!empty($menu['sub_menu'])) echo''; ?>><?php echo $menu["menu"]; echo (!empty($menu['sub_menu']))?'&nbsp;<b class="caret"></b>':'';?></a>
				<?php if (!empty($menu['sub_menu'])) { ?>
					<ul class="dropdown-menu">
						<?php foreach($menu['sub_menu'] as $sub_menu1) { ?>
						<li <?php echo (!empty($sub_menu1['sub_menu']))?'class="dropdown-submenu"':''; ?>>
							<?php if ($is_update) { ?>
							
				<?php if ($is_update) { 
				$visible = $this->getVisibility($sub_menu1["is_visible"]);
						$this->loadToolBox("menu", $sub_menu1['id_menu'], 'Menu', 'parent_menu', $sub_menu1['id_parent_menu'], Array("up", "down","edit", $visible, "delete"), $view_update_link);
				
				
				
				} ?>
							<?php } ?>
							<a href="<?php echo $sub_menu1["url"];?>"><?php echo $sub_menu1["menu"];?></a>
							<?php if (!empty($sub_menu1['sub_menu'])) { ?>
							<ul class="dropdown-menu">
								<?php  foreach($sub_menu1['sub_menu'] as $sub_menu2) { ?>
								<li>
									<?php if ($is_update) { 
													$visible = $this->getVisibility($sub_menu1["is_visible"]);
													$this->loadToolBox("menu", $sub_menu1['id_menu'], 'Menu', 'parent_menu', $sub_menu1['id_parent_menu'], Array("up", "down","edit", $visible, "delete"), $view_update_link);
												} 
									?>
									<a href="<?php echo $sub_menu2["url"];?>"><?php echo $sub_menu2["menu"];?></a>
								</li>
								<?php } ?>
							</ul>
							<?php } ?>
						</li>
						<?php } ?>
					</ul>		
					<?php } ?>			
				</li>
				<?php } ?>	
			<?php if ($is_update) { ?>			
			<li><a class="btn-link" href="#" onclick="exec('Menu/Insert','<?php echo $view_update_link?>');"><b>+</b></a></li>
			<?php }?>
		</ul>
		</div>
		
		</div>
	</div>
</div>