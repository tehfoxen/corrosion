<?
$echo = '';

$echo .= '<div class=entity>';
if($data['access']){
	$echo .= '<div class=entity_mode>';
	$echo .= '<div class="entity_edit aslink" id="edit">Режим редактирования</div>';
	$echo .= '<div><a class="entity_del aslink" href="/data/entitydel/'.route::arg().'">Удалить все данные</a></div>';
	$echo .= '</div>';
}

if(isset($data[0]) && count($data[0])){
	
	foreach($data[0] as $k=>$v){
        $echo .=  '<fieldset><legend>'.$k.'</legend>';
        
		foreach ($v as $key=>$value){
            $echo .=  '<div class="data parametr" id='.$value[2].'_'.$value[1].'_'.$value[3].'>';
			
			if($data['access'] and $value[2]!=0) 
				$echo .=  '<div class="edit_value hide">✎</div>';
			
			$echo .=  '<div class=label>'.$key.'</div>';
			$echo .= '<div class=value>'.$value[0].'</div>';			
			$echo .=  '</div>';
        }
        $echo .=  '</fieldset>';
    } 
} 
$echo .= '</div>';

echo $echo;