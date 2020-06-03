<?
$echo = '';

//вывод формы
if(isset($param_array) && count($param_array)){
	
    $echo .= '<div class="input_anonce hide"></div>';
	$echo .= '<form action="/input/add" method="post" enctype="multipart/form-data" id="spr_form" class=input_data>';
    foreach($param_array as $k=>$v){
        $echo .= '<fieldset><legend>'.$k.'</legend>';
        foreach ($v as $key=>$value){
                $unit = $value[2] ? ', '.$value[2] : '';
                $echo .= '<label for="name">'.$value[1].$unit.'</label>'.$value[3];   
        }
        $echo .= '</fieldset>';
    } 
	$echo .= '
	
	<input type="hidden" name="method_id" value="">
	<div class="send_button">
	<input type="submit"  name="add_data" value="Отправить данные и внести еще образец">&nbsp;&nbsp;&nbsp;
	<input type="submit"  name="add_data" value="Отправить данные"> 
	</div>   
	<img class="loading hide" src="/images/loading.gif" />
	
    </form>';
}        

//Вывод селекта с методами      
if(isset($all_method) && count($all_method)){
    $echo = '<select class=methodlist><option value=""></option>';
    foreach ($all_method as $k=>$v)
        $echo .= "<option value=$k>$v</option>";
    $echo .= '</select>';
}

//Вывод селекта с гостами
if(isset($all_gost) && count($all_gost)){
    $echo = '<select class=gostlist><option value=""></option>';
    foreach ($all_gost as $k=>$v)
        $echo .= "<option value=$k>$v</option>";
    $echo .= '</select>';
}

//Вывод всех параметров с чекбоксами
if(isset($all_param) && count($all_param)){
    $echo .= '<form action="" method="post" enctype="multipart/form-data" class="paramlist input_data" id="spr_form">';
    foreach($all_param as $k=>$v){
        $echo .= '<fieldset><legend>'.$k.'</legend>';
        foreach ($v as $key=>$value){                
                $checked = $value[2]==1 ? 'checked' : '';
                $echo .= '<input type=checkbox name="paramlist[]"'. $checked.' value="'.$value[0].'" />'. $value[1] .'<br>';   
        }
        $echo .= '</fieldset>';
    } 
    $echo .= '<input type="submit" name="send_paramlist" value="показать форму для ввода данных"></form>';
}

//Вывод списка с ранее введенными испытаниями (пункт 3 основного селекта)
if(isset($early_added) && count($early_added)){
	$echo .= '<table class=spr_table id=early><thead><tr><th>Выберите испытание из числа ранее добавленных</th></tr></thead><tbody>';
	foreach ($early_added as $k=>$v){
		$echo .= '<tr><td class="aslink" id='.$k.'>'.$v.'</td></tr>';
	}
	$echo .= '</tbody></table>';	
}

echo $echo;