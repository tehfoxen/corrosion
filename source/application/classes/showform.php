<?
class ShowForm extends Structure{	
	
	public $fieldsform;	
      
    /** Вывод формы добавления/редактирования записи  */
	function getForm(){	
		$this->fieldsform = new fieldsform();  
        $id = $this->arg;
		$f_array = $this->fields;
		$echo = '';
		//парсим название таблицы $f_array[0]
		$ttype =  explode('@',$f_array[0]);	
				
		unset($f_array[0]);		
				
		$legend_title 	= $id ? 'Изменить' : 'Добавить';
		$row 			= $id ? DB::query("SELECT * FROM `$this->table_name` WHERE `id`='$id'")->fetch() : array();
		
		$echo .=  '<fieldset class="'.$this->action_name.' '.$this->table_name.'"><legend>'.$legend_title.'</legend>';
		$echo .=  '<form action="" method="post" enctype="multipart/form-data" id="spr_form" class="'.$this->table_name.'">';		
		
        //var_dump($f_array);
		foreach($f_array as $k=>$v){						
			
			$ftype = explode('@',$v);
			
			//обязательное поле			
			$required = (isset($ftype[2]) and $ftype[2] == 'required') ? 'required' : '';
			
			//заблокированное поле
			$disabled = (isset($ftype[3]) and $ftype[3] == 'disabled' and !$id) ? 'disabled' : '';
			
			// auto поле. В форме не видно, вычисляется отдельно
			$hide = (isset($ftype[3]) and $ftype[3] == 'auto' ) ? 'class="hide"' : '';				
					
			$value = ($id && isset($row[$k])) ? $row[$k] : '';		
						
			$echo .=  "<div $hide >";
			
			$echo .=  '<label>'.$ftype[0].'</label>';
			
			//Обычное текстовое поле
			if($ftype[1]=='text')
				$echo .=  "<input type='text' name='$k' value='$value' $required  $disabled />";
			
			//Поле textarea
			if($ftype[1]=='textarea')
				$echo .=  "<textarea name='$k' $required  $disabled >$value</textarea>";
			
			//Поле даты
			if($ftype[1]=='data'){
				if($id) 
					$value = Helper::ChangeDateFormat($value, 'd.m.Y');
				$echo .=  "<input type='date' class='datepic' name='$k' value='$value' $required $disabled />";
			}
				
			
			//Поле для ввода символов с верхним или нижним регистром
			if($ftype[1]=='symbol'){
				$echo .=  "<div contenteditable='true' class='symbol_field' id ='$k' $required >$value</div>";
				$echo .=  "<input type='hidden' name='$k' value='$value' $disabled />";
			}
			
			//Поле выбора из другой таблицы select
			if($ftype[1]=='select'){				
				$table = explode('_id',$k)[0];
                $where = isset($ftype[5]) ? 'WHERE '.$ftype[5] : '';
                $stmt = DB::query("SELECT `id`, `name` FROM `$table` $where ORDER BY `name`")->fetchAll(PDO::FETCH_KEY_PAIR);				              
                $echo .=  $this->fieldsform->Select($stmt, $k,['selected_value'=>$value,'required'=>$required, 'title'=>' ' ] );
			}
			
			//Поле множественного выбора из другой таблицы selectmulti
			if($ftype[1]=='selectmulti'){
				$f = explode('_id',$k);
				$table = $f[0];
                $selected_value = '';
                if($id){
                    $field1 = $k;
                    $field2 = $this->table_name.'_id';
                    $summary_table = $this->table_name.'__'.$table; 
                    $selected_value = DB::query("SELECT `$field1` FROM `$summary_table` WHERE `$field2`=$id")->fetchAll(PDO::FETCH_COLUMN);
                } 
                    
                //$stmt = DB::query("SELECT `id`, `name` FROM `$table` $where ORDER BY `name`")->fetchAll(PDO::FETCH_KEY_PAIR);  
                $stmt = $this->getArrayGost();
				//echo 'stmt=';var_dump($stmt);echo '<br>';
                $echo .=  $this->fieldsform->SelectMultiple($stmt, $k, 10, ['selected_value'=>$selected_value]);
			}
			
			//Поле autocomplete
			if($ftype[1]=='autocomplete'){
				$f = explode('_id',$k);
				$table = $f[0];
				$val = Queries_db::NameFromTable($table,$value);
				$key = $value ? $value : 'NULL';
				$echo .=  "<input type='text' class='autocomplete_field' placeholder='начните вводить значение' value='$val' id='$k' $required $disabled  />";
			    $echo .=  "<input type='hidden' name='$k' value='$key' />";
			}
				
			
			//Поле enum
			if($ftype[1]=='enum'){
				$echo .=  "<select name='$k' $required $disabled >";
				foreach (Queries_db::GetEnumFromTable($this->table_name, $k) as $key=>$val){
					$select = ($val==$value) ? 'selected' : '';
					$echo .=  "<option value='$val' $select >$val</option>";
				}
				$echo .=  '</select>';
			}
			
            //Поле logical
            if($ftype[1]=='logical'){
				//если значения нет, то вытаскиваем из БД значение по умолчанию для поля.
				if($value == '')
					$value = DB::run("SELECT DEFAULT(`$k`) FROM `$this->table_name` LIMIT 1")->fetchColumn();
				$echo .=  $this->fieldsform->Select(['1'=>'ДА', '0'=>'НЕТ'], $k,['selected_value'=>$value] );
			}
            
			//Поле загрузки файлов
			if($ftype[1]=='file'){
				$echo .=  '<input type="file" name="'.$k.'" />';
			}
			
			$echo .=  '</div>';
		}
		$echo .=  '<div>';
		$echo .=  '<input type="submit" name="'.$this->action_name.'" value="Отправить" />';
		
		
		$echo .=  '</div>';	
		$echo .=  '</form>';		
		$echo .=  '</fieldset>';
		
		return $echo;
			
	}
    
    function getArrayGost(){
        $stmt = DB::run("SELECT t1.id, t1.name as mname, t2.name as gname FROM `method` t1, `gost` t2 WHERE t2.id=gost_id");
        while ($row = $stmt->fetch()){
            $gname = $row['gname'];
			$explode = explode(' ',$gname);
			$gname = $explode[1] ? $explode[1] : $explode[0];
            $mid = $row['id'];
            $opt[$gname][$mid] = $row['mname'];
        }
        return $opt;
    }
    
}