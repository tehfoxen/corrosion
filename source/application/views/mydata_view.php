<? 
//var_dump($data);
$title_page = $data['title_page'] ?? 'Мои данные';?>
<h1 class="spr_title"> <?=$title_page?></h1>

<?
if(count($data[0])){?>

	<table class=spr_table id=mydata>
	<?
	$first = $data[0];
	$first = array_shift($first);
	echo '<thead><tr>';
	foreach($first as $k=>$v)
		echo '<th>'.$k.'</th>';
	echo '<th>показать данные</th></tr></thead>';
	echo '<tbody>';
	foreach ($data[0] as $entity => $field){
		echo '<tr id='.$entity.'>';
			foreach ($field as $fval)
				echo '<td>'.$fval.'</td>';			
		echo '<td> <a href="/data/entity/'.$entity.'" class=fancybox>показать данные</a> </td>';
		echo '</tr>';
	}
	echo '</tbody>';
	?>
	</table>
<?
}
