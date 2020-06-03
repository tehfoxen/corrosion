<?
$echo = '';

$echo .= '<div class=entity>';
if($data['access']){
	$echo .= '<div class=mode>';
	$echo .= '<div class="entity_edit aslink" id="edit">Режим редактирования</div>';
	$echo .= '<div><a class="entity_del aslink" href="/mydata/entitydel/'.route::arg().'">Удалить все данные</a></div>';
	$echo .= '</div>';
}

if(isset($data[0]) && count($data[0])){
  
    foreach($data[0] as $k=>$v){
        $echo .=  '<fieldset><legend>'.$k.'</legend>';
        foreach ($v as $key=>$value){
			$unit = $value[1] ? $value[1] : '';
            $echo .=  '<div class="data parametr" id='.$value[3].'_'.$value[2].'_'.$value[4].'>';
			if($data['access']) 
				$echo .=  '<div class="edit_value hide">✎</div>';
			$echo .=  '<div class=label>'.$key.'</div>';
			$echo .= '<div class=value>'.$value[0].'</div>';
			$echo .= '<div class=unit>'.$unit.'</div>';
			$echo .=  '</div>';
        }
        $echo .=  '</fieldset>';
    } 
} 
$echo .= '</div>';

echo $echo;