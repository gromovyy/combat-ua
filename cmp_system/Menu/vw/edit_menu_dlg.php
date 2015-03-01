<div class="modal">
  <div class="modal-header">
    <a class="close" onclick='RemoveDlg("Menu_edit_menu_dlg");'>×</a>
    <h3>Редактирование меню</h3>
  </div>
  <div class="modal-body">
    <p>
				<br><span>Имя пункта меню</span>
				<?php $this->Input('text', 'mn_menu', 'menu', $menu['id_menu'], $menu["menu"], Array(),$menu["id_owner"]);?>
				<br><br><span class='linkname'>Ссылка(url)</span>
				<?php $this->Input('text', 'mn_menu', 'url', $menu['id_menu'], $menu["url"], Array(), $menu["id_owner"]);?>
				<br><br><span class='linkname'>Родительское меню</span>
				<?php $this->Input('combobox', 'mn_menu', 'id_parent_menu', $menu['id_menu'], $menu['id_parent_menu'],array('valueList' => $combo_menu_list), $menu["id_owner"]);?>
	</p>
  </div>
  <div class="modal-footer">
    <a href="#" class="btn" onclick='update("Menu/MainMenu");RemoveDlg("Menu_edit_menu_dlg");' >OK</a>
  </div>
</div>