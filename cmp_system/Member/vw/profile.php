<?php // print_r($member);?>
<div class="profile">
	<div class="row-fluid clearfix">
		<div class="col-xs-12 h3"><?php echo $_['1'];?></div>
	</div>
	<div class="row-fluid clearfix">
		<div class="col-xs-2 h4"><?php echo $_['2'];?></div>
		<div class="col-xs-10"><?php $this->Input('text', 'mmbr_member', 'name', $member['id_member'], $member["name"], Array(), $member["id_owner"]);?></div>
	</div>
	<div class="row-fluid clearfix">
		<div class="col-xs-2 h4"><?php echo $_['3'];?></div>
		<div class="col-xs-10"><?php $this->Input('text', 'usr_user', 'email', $member['id_member'], $user["email"], Array(), $member["id_owner"]);?></div>
	</div>
	<div class="row-fluid clearfix">
		<div class="col-xs-2 h4"><?php echo $_['4'];?></div>
		<div class="col-xs-10"><?php $this->Input('text', 'mmbr_member', 'phone', $member['id_member'], $member["phone"], Array(), $member["id_owner"]);?></div>	
	</div>
	<div class="row-fluid clearfix">
		<div class="col-xs-2 h4"><?php echo $_['5'];?></div>
		<div class="col-xs-10"><?php $this->Input('password', 'usr_user', 'password', $member['id_member'], "*****", Array(), $member["id_owner"]);?></div>
	</div>
	<div class="row-fluid clearfix">
		<div class="col-xs-12">
			<button class="btn btn-danger" onclick="loadDlg('Member/DeleteDlg/<?php echo $member['id_member'];?>')"><?php echo $_['10'];?></div>
		</div>
	</div>
</div>
