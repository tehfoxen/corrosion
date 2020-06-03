<?
$search_str = $_REQUEST['search'] ?? '';
?>
<h1 class="spr_title">Поиск</h1>
<form method=get class=quick_search>
<input type=text name=search value="<?=$search_str?>"  />
<input type=submit value="найти" />
</form>
<?
//var_dump($data);
if(isset($data)) {
	if(count($data)){
		
		echo '<div class=count_search>Найдено совпедений: '.count($data).'</div>';
		$copy_data = $data;
		$titles = array_shift($copy_data);
		ksort($titles['title']);
		
		echo '<table class=spr_table id=searchresult><thead><tr>';
		foreach ($titles['title'] as $k=>$v)
			echo '<th>'.$k.'</th>';
		echo '<th>Поисковая фраза найдена в параметрах</th><th>показать данные</th></tr></thead><tbody>';
		
		foreach($data as $entity_id=>$arr){
			$param_str = [];
			ksort($arr['title']);
			
			echo '<tr>';
			foreach($arr['title'] as $k=>$v)
				echo '<td>'.$v.'</td>';
			foreach ($arr['search_result'] as $k=>$v)
				$param_str[] = $k.': '.$v;
			echo '<td>'. implode ('<br>',$param_str) .'</td>';
			echo '<td> <a href="/data/entity/'.$entity_id.'" class=fancybox>показать данные</a> </td>';
			echo '</tr>';
		}
		echo '</tbody></table>';
	}
	else echo '<div class=error>Ничего не найдено</div>';
}
