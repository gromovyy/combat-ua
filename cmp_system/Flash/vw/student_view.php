<?php
	$this->includeJS("jquery.tools.min.js");
?>
<div class="flash-preview">
		<center>
			<div id="flash<?php echo $flash["id_flash_bind"];?>" style='width:100%; height:100%; background-color:#CCCCCC; position:relative;'
			   onclick = 'flashembed("flash<?php echo $flash["id_flash_bind"];?>", {src: "<?php echo $url_swf;?>?loop=0&autoplay=0&api=1", wmode: "opaque"}); $("#stop<?php echo $flash["id_flash_bind"];?>").css("display","block");' >
			</div>
			<div id="stop<?php echo $flash["id_flash_bind"];?>" onclick="$('#flash<?php echo $flash["id_flash_bind"];?>').html(''); $(this).css('display','none');" style="display:none; background-color:red; color:white; cursor:pointer">Зупинити FLASH-ролик</div>
		</center>
</div>