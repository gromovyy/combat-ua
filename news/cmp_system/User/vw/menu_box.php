<span class="nav">  


  <?php 		 if ($this->User->getRole() == "unregistered"){?>
  
  <div class="btn btn-navbar visible-desktop visible-tablet" onclick="loadDlg('EmailPasAuth/RegisterForm');">
			Реєстрація
			</div>  
				<div class="btn btn-navbar visible-desktop visible-phone visible-tablet" onclick="loadDlg('EmailPasAuth/LoginForm');">
			Вхід
			</div>
			
		<?php }else{ ?>
		<li>
				<?php echo $this->Member->e_MemberLink($this->User->getId());?>								
							
				</li>
		
		<?php if (($this->User->getRole()=="administrator" or true) /*or ($this->User->getRole()!="unregistered" and $this->is_edit)*/) { ?>
					<a class="pull-left" href="<?php echo $this->url('User', 'ChangeEditMode');?>">
						<?php echo ($this->User->getMode()=="edit")? '<i class="icon-eye-open"></i>':'<i class="icon-edit"></i>';?>
					</a>
				<?php } ?>
				
		<a href="<?php echo $this->url('User', 'SecurityCancel');?>"> 
		  <span class="btn btn-navbar visible-desktop visible-phone visible-tablet pull-right" onclick="">
			Вихід
			</span>  
			</a>
			
			
			
			
        
			
		<?php }	?>
</span>