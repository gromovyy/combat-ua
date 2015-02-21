<?php include("header.php"); ?>


<div class="container-fluid layout" >
	<div class="row-fluid">
		<div class="span12">
			<?php $this->loadPosition("position1", "workLayout"); ?>
		</div>
	</div>

	<div class="row-fluid main-layout">				
		<div class="span9 left-position">
			<?php $this->loadPosition("position2", "workLayout"); ?>
		</div>
		<div class="span3 right-position">
			<?php $this->loadPosition("position3", "workLayout"); ?>
		</div>
	</div>
</div>	

<?php include("footer.php");?>