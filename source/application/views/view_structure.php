<?
$edit = route::action_name() == 'edit' ? 1 : 0;
$hide = $edit ? '' : 'hide';

if(isset($data['error']))
	echo '<div class=error>'.$data['error'].'</div>';

if(!$edit)
	echo '<div class=title_form_structure>Добавить новую запись</div>';

echo '<div class=line>';     
        
	echo '<div class="getform '.$hide.'" >';
        echo $data[0];
		if($edit) 
			echo '<br><a class=add href="/'.route::controller_name().'/add">Добавить новую запись</a>';
    echo '</div>';   


    echo '<div>';
        echo $data[1];
    echo '</div>';

echo '</div>';