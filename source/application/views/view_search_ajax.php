<?
$echo = '';

//форма задания значений параметров
if(isset($data['choose_param'])){
	$echo .= '<form method="post" name=search class="choose_param_list">';
	$obj = new fieldsform();
	foreach ($data['choose_param'] as $param_id=>$array){
		
		$echo .= '<div class=choose_param id='.$param_id.'>';
		foreach ($array as $param_name=>$select){
			
			$echo .= '<div class=choose_select>'.$param_name.'<br>';
			//$echo .= $obj->Select($select, 'query_'.$param_id, ['title'=>'', 'class'=>'query', 'required'=>'required', 'id'=>$param_id]);	
			$echo .= $obj->Select($select, 'query_'.$param_id, ['selected_value'=>1,'class'=>'query','id'=>$param_id]);				
			$echo .= '</div>';
			$echo .= '<div class=sort title="переместите для сортировки">⇅</div><div class=del title="удалить параметр из поиска">✖</div>';
		}
		$echo .= '</div>';
	}
	$echo .= '<input type=submit name="queries_array" /> </form>';
}

//Результаты поиска
if(isset($data['search_result'])){
	//var_dump($data['search_result']);
	if(is_array($data['search_result'])){
		$num = count($data['search_result'])-1;
		$echo .= '<div class="count_search">Найдено записей: '.$num.'</div>';
		$echo .= '<table class=spr_table id=searchresult><thead>';
		foreach ($data['search_result'] as $key=>$value){
			$echo .= '<tr>';			
			foreach ($value as $k=>$v){
				//echo '<br>'.$value.'--'.$k.'--'.$v.'<br>';
				$paramid = '';
				$tag = 'td';
				if($key === 'title'){
					$tag = 'th';
					$explode = explode('@',$v);
					if(count($explode)>1){
						$v = $explode[1];
						$paramid = ' id=param_'.$explode[0];
						$tag = 'th id=param_'.$explode[0];
					}
				}
				if($v == 'entity_id'){
					$v = 'показать данные';
					$tag .= ' class=tablesorter-noSort ';
					$n = $k;
				}
				if($k == $n and $key!=='title')
					$v = '<a href="/data/entity/'.$v.'" class="fancybox">показать данные</a>';
					
				$echo .= '<'.$tag.'>'.$v.'</'.$tag.'>';
			}
			$echo .= '</tr>';
			if($key === 'title') 
				$echo .= '</thead><tbody>';
		}
		$echo .= '</tbody></table>';
	}
	else
		$echo .= '<div class=error>'.$data['search_result'].'</div>';
}
echo $echo;
