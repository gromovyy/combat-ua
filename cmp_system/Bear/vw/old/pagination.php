<?php if (count($pages)>1) { ?>
<div class="paginate">
		<ul class="pagination">
			<li class="disabled"><a href="http://<?php echo $_SERVER["SERVER_NAME"].strtok($_SERVER["REQUEST_URI"],'?');?>?page=1">&laquo;</a>
			</li>
			<?php foreach($pages as $page) { ?>
			<li <?php if ($page == $current_page) echo 'class="active"';?>><a href="http://<?php echo $_SERVER["SERVER_NAME"].strtok($_SERVER["REQUEST_URI"],'?');?>?page=<?php echo $page;?>"><?php echo $page;?></a>
			</li>
			<?php } ?>
			<li><a href="<?php if ($_POST['isAjax'] != 1) 
											echo 'http://'.$_SERVER["SERVER_NAME"].strtok($_SERVER["REQUEST_URI"],'?');
									  else 
											echo $_SERVER["HTTP_REFERER"];
									?>?page=<?php echo $page;?>	">&raquo;</a>
			</li>
		</ul>
</div>
<?php } ?>