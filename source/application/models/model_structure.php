<?
class Model_Structure extends Structure
{
   function __construct(){
		parent::__construct();
        $this->files = new files($this->table_name);       
	}   
   
 /** Добавление/Редактирование записей  */
	public function change_data(){		
        $f_array = $this->fields;
        $id = $this->arg;
		$string_arr = []; 		
    
		unset($f_array[0]);	
		unset($f_array['file']); //будет обновляться через метод FileUpload
        
		foreach($f_array as $k=>$v){			
			
            $val = $_POST[$k];
			if($k == 'unit_id' and $val == '')
				$val = 'NULL';
			//echo '$k='.$k.'---$v='.$v.'---$val='.$val.'<br>';
			$ftype = explode('@',$v);
			
			//если поле дата - форматируем
			if($ftype[1]=='data') $val = Helper::ChangeDateFormat($val, 'Y-m-d');
			
			//если поле символьное - удаляем тэги
			if($ftype[1]=='symbol') $val = strip_tags($val,'<sub><sup>');
			
			//если поле selectmulti - собираем в строку выбранные значения селекта
			if($ftype[1]=='selectmulti') {
				$table_multi = explode('_id',$k)[0];
                $val_multi = $val;                
            }
            
			if($ftype[1]!='selectmulti' and $val!=''){
				if($val!='NULL')
					$val=DB::quote(trim($val));
				$string_arr[] = "`$k` = $val";
			}
		}
		$string = implode(',',$string_arr);    
		
		if($id){
			$query = "UPDATE `$this->table_name` SET $string WHERE `id`='$id'";
			helper::WriteLog('changes'); //надо перенести
		}		
			
		else
			$query = "INSERT INTO `$this->table_name` SET $string";
		
		
		$rs = DB::exec($query);			
		if(!$rs)
			throw new Exception();
		if($id){			
			helper::WriteLog('changes'); 
		}	
		
		
		$lastID = ($id) ? $id : DB::lastInsertId();
		$this->lastID = $lastID; 
		
        //таблица user (код активации и отправка письма-подтверждения)
		if(!$id and $this->table_name == 'user'){
            $obj = new user();  
			$obj->UserReg($lastID);
        }  

		//если поле selectmulti - грузим данные в сводную таблицу
        if(count($val_multi)){
            //var_dump($val_multi);die;
            $field1 = $table_multi.'_id';
            $field2 = $this->table_name.'_id';
            $summary_table = $this->table_name.'__'.$table_multi; 
            //Удаляем то, что было для данного параметра
            $rs = DB::run("DELETE FROM `$summary_table` WHERE `$field2` = $lastID");
            //Вносим заново
            foreach ($val_multi as $v){
                 $rs = DB::run("INSERT INTO `$summary_table` SET $field1 = $v , `$field2` = $lastID");
            }
        }
        
        //значение для авто поля
		//$this->ValueAutoField($lastID);		
		
		//если есть файлы для загрузки
         if(isset($_FILES) and $_FILES['file']['size']!=0){                             
              $this->files->FileUpload($lastID);			
         }
	}
    
     public function del_data(){
            $stmt = DB::run("DELETE FROM `$this->table_name` WHERE id=?", [$this->arg]);
			$this->files->FileDelete($this->arg);
			helper::WriteLog('changes');        
    }



    
       
    
}