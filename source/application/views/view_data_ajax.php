<?
if(isset($field)){
	$btn = '<button class=edit>отправить</button><button class=del>удалить</button><button class=cancel>отменить</button>';
	$err = '<div class=error></div>';  
	echo $field.'&nbsp;'.$btn.$err; 
} 