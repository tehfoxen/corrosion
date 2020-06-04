<?
$search_str = $_REQUEST['search'] ?? '';
?>

<form method=get class=quick_search>
	<input type=text name=search value="<?=$search_str?>"  />
	<input type=submit value="найти" />
</form>

	<?
//Быстрый поиск. результаты
if(isset($data['quick_search'])) {
	if(count($data['quick_search'])){
		
		echo '<div class=count_search>Найдено совпадений: '.count($data['quick_search']).'</div>';
		$copy_data = $data['quick_search'];
		$titles = array_shift($copy_data);
		ksort($titles['title']);
		
		echo '<table class=spr_table id=searchresult><thead><tr>';
		foreach ($titles['title'] as $k=>$v)
			echo '<th>'.$k.'</th>';
		echo '<th>Поисковая фраза найдена в параметрах</th><th class=tablesorter-noSort>показать данные</th></tr></thead><tbody>';
		
		foreach($data['quick_search'] as $entity_id=>$arr){
			$param_str = [];
			ksort($arr['title']);
			
			echo '<tr>';
			foreach($arr['title'] as $k=>$v)
				echo '<td>'.$v.'</td>';
			foreach ($arr['search_result'] as $k=>$v)
				$param_str[] = '<i>'.$k.'</i>:&nbsp;&nbsp;&nbsp; '.$v;
			echo '<td>'. implode ('<br>',$param_str) .'</td>';
			echo '<td> <a href="/data/entity/'.$entity_id.'" class=fancybox>показать данные</a> </td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
	else echo '<div class=error>Ничего не найдено</div>';
}?>

