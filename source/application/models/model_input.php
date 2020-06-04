<?
class Model_Input extends Model{
    
    //формирование поля формы в зависимости от типа данных
	public function GetField($datatype,$paramid,$multiselect,$required,$title=NULL,$value=NULL){
        $obj = new fieldsform();
        $fieldname = 'param_'.$paramid;
        $required = $required ? 'required' :'';
        switch ($datatype) {
                case 1: 
					$field = $obj ->Text('text', $value, $fieldname, ['class'=>'itext', 'required'=>$required, 'placeholder'=>'целое число']);break;
				case 2:
					$field = $obj ->Text('text', $value, $fieldname, ['class'=>'itext', 'required'=>$required]);break;
                case 5:
                    //, 'placeholder'=>'не более 255 символов'
					$field = $obj ->Text('text', $value, $fieldname, ['class'=>'itext', 'required'=>$required, 'maxlength'=>255]);break;
                case 3:
                    $field = $obj ->Text('text', $value, $fieldname, ['class'=>'idate', 'required'=>$required]);break;
                case 7:
				case 9:
					$field = $obj ->Text('file', $value, $fieldname, ['class'=>'ifile', 'required'=>$required]);break;
                case 6:
                    $field = $obj ->TextArea($value, $fieldname);break;
                case 4:
					$field = $obj ->Select(Helper::LogicalArray(), $fieldname, ['title'=>$title, 'required'=>$required, 'selected_value'=>$value]); break;           
                case 8:
                    $list_array = helper::DiscreteValueList($paramid);
					
					if($paramid == 'gost')
						//$list_array = $this->GetGost();
						$list_array = helper::GetGost();
                    if($paramid == 'method')
						$list_array = helper::getArrayGost();
					
					if($multiselect == 1)
                        $field = $obj ->SelectMultiple($list_array, $fieldname, 15, ['required'=>$required, 'selected_value'=>$value]);
                    else
                        $field = $obj ->Select($list_array, $fieldname, ['title'=>$title, 'required'=>$required, 'selected_value'=>$value]);
                    break;
        }
        //echo $field;
		return $field;
    }
    
    //Выбор пункта 3 в основном селекте. Собираем уникальные испытания пользователя. По сути выкидываем лишнее из таблицы в разделе "Мои Данные" 
	function GetEarlyAdded(){
		include 'application/models/model_mydata.php';
		$obj = new Model_Mydata();		
		$mydata = $obj->GetMyData($_SESSION['user_id']);
		$key = 'Описание испытания';
		$early_array = Helper::array_unique_key($mydata, $key); 

		foreach ($early_array as $entity_id=>$value)			
			$data[$entity_id] = $value[$key];
		
		//var_dump($early_array);
		//var_dump($data);
		return $data;
	}
	
	//Список id параметров, принадлежащих конкретной сущности
	function GetParamList($entity_id){
		//из таблицы data
		$param_array1 = DB::run("SELECT `parametr_id` FROM `data` WHERE `entity_id` = ?",[$entity_id])->fetchAll(PDO::FETCH_COLUMN);		
		
		//из таблицы data_discrete
		$param_array2 = DB::run("SELECT `parametr_id` FROM `data_discrete` WHERE `entity_id` = ?",[$entity_id])->fetchAll(PDO::FETCH_COLUMN);
		
		//из файловой структуры
		include 'application/classes/files.php';
		$obj = new Files('input');	
		$param_array3 = $obj->FileEntity($entity_id);
		
		$param_array = array_merge ($param_array1,$param_array2,$param_array3);
		//var_dump($param_array);
		
		$paramlist = implode(',',$param_array);
		return $this->GetForm($paramlist);
	}
	
	//формируем массив с параметрами для вывода в форме
    public function GetForm($paramlist=NULL){
        //обязательные параметры
        $require = "
        UNION
        SELECT t1.id, t1.name as paramname, t4.name as category, t4.`order` as catorder, t1.datatype_id, t3.name as unit_name, t1.multiselect, t1.required 
        FROM `parametr` t1 
        LEFT JOIN `unit` t3 
            ON t1.unit_id = t3.id 
        LEFT JOIN `category` t4 
            ON t1.category_id = t4.id 
        WHERE t1.required=1";
        
        $orderby = ' ORDER BY catorder ASC, paramname ASC';
        
        if(!$paramlist)
            $query = "
        SELECT t1.id, t1.name as paramname, t4.name as category, t4.`order` as catorder, t1.datatype_id, t3.name as unit_name, t1.multiselect, t1.required 
        FROM `parametr` t1 
        LEFT JOIN `parametr__method` t2 
            ON t1.id = t2.parametr_id 
        LEFT JOIN `unit` t3 
            ON t1.unit_id = t3.id 
        LEFT JOIN `category` t4 
            ON t1.category_id = t4.id 
        WHERE t2.method_id =".route::arg();
        else
            $query = "
        SELECT t1.id, t1.name as paramname, t4.name as category, t4.`order` as catorder, t1.datatype_id, t3.name as unit_name, t1.multiselect, t1.required 
        FROM `parametr` t1         
        LEFT JOIN `unit` t3 
            ON t1.unit_id = t3.id 
        LEFT JOIN `category` t4 
            ON t1.category_id = t4.id 
        WHERE  t1.id IN ($paramlist)";    
                
        $query = $query.$require.$orderby;
		//echo $query;
        
        $param_array = [];        
        
        $stmt = DB::run($query);
		while ($row = $stmt->fetch()){
            $category =  $row['category'];
            $field = $this->GetField($row['datatype_id'],$row['id'],$row['multiselect'],$row['required'],'');
            $param_array[$category][] = array($row['id'],$row['paramname'],$row['unit_name'],$field);
        }
        
        return $param_array;
    }
    
    
    //все существующие параметры для выбора 
    public function GetAllParam($where=NULL){
		$query = "
        SELECT t1.id, t1.name, t2.name as category, t1.required
        FROM `parametr` t1        
        LEFT JOIN `category` t2 
            ON t1.category_id = t2.id         
        $where 
		ORDER BY t2.`order` ASC, t1.`name` ASC";
        $stmt = DB::run($query);
        while ($row = $stmt->fetch()){
            $category =  $row['category'];            
            $param_array[$category][] = array($row['id'],$row['name'],$row['required']);
        }
        return $param_array;
    }
	
	//Добавление данных
	public function DataAdd(){
		if(isset($_POST)){
			//var_dump($_POST);			
			//1. Пишем в entity сущность и получаем id сущности	
			$method_id = $_POST['method_id'] ? (int)$_POST['method_id'] : NULL;
				 
			$stmt = DB::run("INSERT INTO entity (user_id, method_id) VALUES (?,?)", [$_SESSION['user_id'],$method_id]); 
			$entity_id = DB::lastInsertId();
			
			//2. записываем данные POST
			foreach ($_POST as $param => $value){
				if($value!=''){
					$paramid = explode('_',$param)[1];
					$datatype = Helper::DataTypeEName($paramid);
					if($datatype){
						//дискретные
						if($datatype == 'discrete') {
							if(is_array($value)){ 						
								$stmt = DB::prepare("INSERT INTO `data_discrete` (`discrete_id`,`entity_id`,`parametr_id`) VALUES (?,?,?)");
								foreach ($value as $discrete_id)
									$stmt->execute([$discrete_id, $entity_id, $paramid]);
							}
							else
								$stmt = DB::run("INSERT INTO `data_discrete` (`discrete_id`,`entity_id`,`parametr_id`) VALUES (?,?,?)", [$value,$entity_id,$paramid]);
						}					
						else{ //остальные
							if($datatype == 'datetime'){
								if(!Helper::validDate($value)) {
									//echo 'неверный формат даты';
									//die;
									throw new Exception('Неверный формат даты');
								}
								else
									$value = Helper::ChangeDateFormat($value,'Y-m-d');
							}
								
							if($datatype == 'float'){
								$value = str_replace(',' , '.' , $value );
							}
							 
							$field = $datatype.'_value';
							$stmt = DB::run("INSERT INTO `data` (`$field`,`parametr_id`,`entity_id`) VALUES (?,?,?)", [$value,$paramid,$entity_id]);
						}		
					}
				}
			}
			//3. Записываем изображения и файлы
			if(isset($_FILES)){
				foreach ($_FILES as $key=>$value){
					if($_FILES[$key]['size']!=0){
						$paramid = explode('_',$key)[1];
						$obj = new files(route::controller_name());
						$obj->FileUpload($entity_id.'_'.$paramid,$key);
					}
				}
			}
		}
	}
	
}