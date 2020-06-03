<h1 class="spr_title">Мои данные</h1>

<div class=entity>
<?
if(isset($data) && count($data)){
  
    foreach($data as $k=>$v){
        echo '<fieldset><legend>'.$k.'</legend>';
        foreach ($v as $key=>$value){
            $unit = $value[1] ? ' '.$value[1] : '';
            echo '<div class=parametr id='.$value[2].'>';
			echo '<div class=label>'.$key.'</div>';  
			//echo '<div class=label>'.$key.'</div>'.$value[0].$unit; 
			echo '<div class=value>'.$value[0].'</div>';
			echo '<div class=unit>'.$unit.'</div>';
			echo '</div>';
        }
        echo '</fieldset>';
    } 
	
} 
?>
</div>

