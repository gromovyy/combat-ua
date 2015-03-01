<div>
<?php
		if (!empty($data["photo"]["url_photo"])){
			$this->loadView("photo_view_photo",$data);
		}else{
			$this->loadView("photo_view_empty",$data);
		}
?>
</div>