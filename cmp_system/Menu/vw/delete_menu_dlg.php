<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Menu_delete_menu_dlg");'>×</a>
    <h3>Удаление меню</h3>
  </div>
  <div class="modal-body">
    <p>
		Вы действительно хотите удалить пункт меню
					 <br><br>
					 <h4><?php echo $menu["menu"];?></h4>
					 <br><br>И все его подпункты?
					 
	</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" onclick='RemoveDlg("Menu_delete_menu_dlg");' >Нет</a>
    <a href="#" class="btn btn-danger" onclick='exec("Menu/Delete/menu/<?php echo $menu["id_menu"];?>","Menu/MainMenu");RemoveDlg("Menu_delete_menu_dlg");'>Да</a>
  </div>
</div>