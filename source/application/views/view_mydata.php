<? 
//var_dump($data[0]);
$num = count($data[0]);
if($num){
	echo '<div class="count_search">Всего записей: '.$num.'</div>
	<table class=spr_table id=mydata>';
	
	$first = $data[0];
	$first = array_shift($first);
	echo '<thead><tr>';
	foreach($first as $k=>$v)
		echo '<th>'.$k.'</th>';
	echo '<th class=tablesorter-noSort>показать данные</th></tr></thead>';
	echo '<tbody>';
	foreach ($data[0] as $entity => $field){
		echo '<tr id='.$entity.'>';
			foreach ($field as $fval)
				echo '<td>'.$fval.'</td>';			
		echo '<td> <a href="/data/entity/'.$entity.'" class=fancybox>показать данные</a> </td>';
		echo '</tr>';
	}
	echo '</tbody></table>';
	
}?>
