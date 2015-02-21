<?php
$type = $extra['type'];

switch ($type)
{
	case 'insert-checkmark':
		switch ($value) {
			case 1:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: 0})\"";
						$class = "insert yes";break;
			default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: 1})\"";
						$class = "insert no";break;
		}break;
	case 'select-checkmark':
		switch ($value) {
			case 1:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: 0})\"";
						$class = "select yes";break;
			default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: 1})\"";
						$class = "select no";break;
		}break;
	case 'update-checkmark':
		switch ($value) {
			case 1:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: 2})\"";
						$class = "update yes";break;
			case 2:		$js = "onclick=\"ajax('Article/editCell/$id', {value: 0})\"";
						$class = "update owner";break;
			default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: 1})\"";
						$class = "update no";break;
		} break;
	case 'visibility-checkmark':
		switch ($value) {
			case 1:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: 2})\"";
						$class = "visibility yes";break;
			case 2:		$js = "onclick=\"ajax('Article/editCell/$id', {value: 0})\"";
						$class = "visibility owner";break;
			default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: 1})\"";
						$class = "visibility no";break;
		} break;
	case 'delete-checkmark':
		switch ($value) {
			case 1:  	$js = "onclick=\"ajax('Article/editCell/$id', {value: 2})\"";
						$class = "delete yes";break;
			case 2:		$js = "onclick=\"ajax('Article/editCell/$id', {value: 0})\"";
						$class = "delete owner";break;
			default:    $js = "onclick=\"ajax('Article/editCell/$id', {value: 1})\"";
						$class = "delete no";break;
		} break;

}

if ( ($type=="select-checkmark") or ($type =="insert-checkmark") or ($type =="visibility-checkmark") 
	 or ($type == "update-checkmark") or ($type == "delete-checkmark"))
	echo '<div id="'.$id.'" class="contenter-input '.$class.'" '.(($is_update)?$js:"").'></div>';


?>