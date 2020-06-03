<?
/** Набор отображений различных полей формы */
class FieldsForm{
   
 function Select(array $value_array, string $selectName, $options=array()) {
       /**
       1. array $value_array :    массив значений для селекта $key=>$value
       2. string $selectName :    название поля name
       3. selected_value :       значение из списка, которое должно быть выделено  
       4. string $title :         первый пустой option: заголовок селекта 
       5. string $id :            название id селекта  
       6. string $class:          название класса селекта
       7. string $wraper :        тэг обертки селекта 
       8. string $wraper_class :  класс обертки селекта 
       пример вызова функции: echo $obj->Select([...], $name, ['selected_value'=>$val, 'maxlength'=>'100', 'class'=>'required'])
       */
       
       $default_options = array(
        'selected_value'=>null,
        'title'=>null,
        'id'=>null,
        'class'=>null,
        'wraper'=>null,
        'wraper_class'=>null,
        'required'=>null
        );
    
       $result = array_merge($default_options, $options);       
       extract($result);
       
       $echo = '';
       if($wraper){
           $wclass = ($wraper_class) ? 'class = "'.$wraper_class.'"' : '';
           $echo .= '<' . $wraper . ' ' . $wclass . '>';
       } 
       
       $id = $id ? ' id = "' .$id.'"' : '';
       $class = $class ? ' class = "' .$class.'"' : '';
       $required = $required ? ' required' : '';
       
       $echo .= '<select name = "'.$selectName.'"'.$id.$class.$required.' >';
       
       if(isset($title)){
		  // var_dump($selected_value);
		   $selected = $selected_value == null ? 'selected' : '';
		   $echo .= '<option value=""'.$selected.'>'.$title.'</option>';
	   }
           
	   foreach ($value_array as $key=>$value) {
            //echo 'key='.$key.' value='.$value.'selected_value='.$selected_value.'<br>';			
			//echo 'key=';var_dump($key);
			$selected = '';
			if($selected_value !== null)				
				$selected = (isset($key) and $selected_value == $key) ? ' selected ' : '' ;		
            $echo .= '<option value="'.$key.'"'.$selected.'>'.$value.'</option>';		
        }
       
       $echo .= '</select>';
       
       if($wraper)
           $echo .= '</' . $wraper . '>';
        
        return $echo;
   }  
   
   
   
 function SelectMultiple(array $value_array, string $selectName, $size, $options=[]) {
       /**
       1.array    $value_array :    массив значений для селекта $key=>$value
       2.string   $selectName :     название поля name 
       4.integer  $size :           размер списка (число видимых значений)    
       3.string   $title :          первый пустой option: заголовок селекта 
       5.$selected_value :          значение из списка, которое должно быть выделено 
       6.string   $id :             название id селекта 
       7.string   $class :          класс селекта       
       8.string   $wraper :         тэг обертки селекта 
       9.string   $wraper_class :   класс обертки селекта 
       */
       
       $default_options = array(
        'selected_value'=>[],
        'title'=>null,
        'id'=>null,
        'class'=>null,
        'wraper'=>null,
        'wraper_class'=>null,
        'required'=>null
        );
    
       $result = array_merge($default_options, $options);       
       extract($result);
       
       $echo = '';
       if($wraper){
           $wclass = $wraper_class ? 'class = "'.$wraper_class.'"' : '';
           $echo .=  '<' . $wraper . ' ' . $wclass . '>';
       } 
       
       $id = $id ? ' id = "' .$id.'"' : '';
       $class = $class ? ' class = "' .$class.'"' : '';
       $required = $required ? ' required' : '';
       
       $echo .=  '<select name = "'.$selectName.'[]" size="'.$size.'" multiple="multiple" '. $id . $class . $required . '>';
       
       if($title)
           $echo .= '<option value="">'.$title.'</option>';
       
       foreach ($value_array as $key=>$value) {
            //многомерный массив с разбиением на группы
            if(is_array($value)){                
                $echo .= "<optgroup label=$key>";
                foreach($value as $k=>$v){
                    $selected = (!empty($selected_value) and in_array($k, $selected_value)) ? ' selected ' : '';
                    $echo .=  "<option value=$k $selected > $v </option>";
                }
                $echo .=   '</optgroup>';
            }
            else{
                $selected = (!empty($selected_value) and in_array($key, $selected_value)) ? ' selected ' : '';
                $echo .=  "<option value=$key $selected > $value </option>";	
            }
       }
       
       $echo .=  '</select>';
       //$echo .=  '<div class=ctrl_select>При выборе нескольких вариантов удерживайте нажатой клавишу Ctrl</div>';
       
       if($wraper)
           $echo .=  '</' . $wraper . '>';
       
       return $echo;    
   }
   
   
 function Text($type, $value, string $name, $options=array()) {
       /*
       1. $type             тип (text, hidden, submit и пр.)
       2. $value -          значение
       3. string $name      имя поля       
       4. string $maxlength максимальное число символов для ввода в поле
       5. string $id        id поля
       6. string $class     класс поля
       7. placeholder      плейсхолдер
       8. required
       
       пример вызова функции: echo $obj->Text('text', $name, 'name', array('maxlength'=>'100', 'required'=>'required'))
       */
        $default_options = array(
        'maxlength'=>null,
        'placeholder'=>null,
        'required'=>null,
        'id'=>null,
        'class'=>null
        );
    
       $result = array_merge($default_options, $options);
       
       $options_string = '';
       foreach ($result as $k=>$v){
            if($v){
                $options_string .= ($k != $v) ? $k.'= "'.$v.'" ' : $k.' ';
            }                        
        }
        
       $echo =  "<input type='$type' name='$name' value='$value' $options_string />";       
       return $echo;
   }
   
   function TextArea( $value, string $name, string $class = '', $disabled = 0 ) {
       // $disabled: 1-disabled; 0 - НЕ disabled 
       
       $disabled = ($disabled==1) ? 'disabled = true' : '';
       $class = ($class) ? 'class="'.$class.'"' : '';
       $echo = '<textarea name="'.$name.'" '.$disabled.' '.$class.'>'.$value.'</textarea>';
       return $echo;
   }
   
   function Switches($type, string $name, $value, int $checked=0, string $id = '', string $class = '' ) {
       /*
       1. $type             тип (radio/checkbox)
       2. string $name      имя поля
	   3. $value -          значение
       4. int $checked		1-выделено, 0-не выделено
       5. string $id        id поля
       6. string $class     класс поля
       */
       
       global $$name;
	   $id          = $id          ? 'id="'.$id.'"' : '';
       $class       = $class       ? 'class="'.$class.'"' : '';
       
       $checked = ($$name==$value or $checked==1) ? 'checked' : '';
       $echo =  '<input type="'.$type.'" name="'.$name.'" value="'.$value.'" '.$checked.' '.$id.' '.$class.' />';       
       return $echo;
   }
    
    
}
?>