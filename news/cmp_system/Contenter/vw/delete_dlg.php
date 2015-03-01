<div>
	Действительно хотите удалить объект <?php echo $object ?>(<?php echo $id_object ?>) из компонента <?php echo $component ?> 
	<button onclick="RemoveDlg('Contenter_delete_dlg');">Нет</button>
	<button onclick="exec('<?php echo $component;?>/Delete/<?php echo $object."/".$id_object;?>,'<?php echo $update_link;?>');RemoveDlg('Contenter_delete_dlg');">Удаляй его полностью</button>
</div>