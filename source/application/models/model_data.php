<?
class Model_Data extends Model{
    
    function __construct(){		
        $this->user_session = $_SESSION['user_id'];		
	} 
	
	//получаем поле для редактируемого параметра
	function GetField(){
		include 'application/models/model_input.php';
		$obj = new Model_Input();
		$param_id = route::arg();
		$data_type = $_POST['data_type'];
		$value = $_POST['value'];
		$entity = $_POST['entity'];		
		
		if($data_type == 'discrete' || $data_type == 'multidiscrete'){
			$value = DB::run("SELECT discrete_id FROM `data_discrete` WHERE entity_id=? AND parametr_id=?",[$entity,$param_id])->fetchAll(PDO::FETCH_COLUMN);
			if($data_type == 'discrete')
				$value = $value[0];
		}
		if($data_type=='logical'){
			$value = array_search($value, Helper::LogicalArray());
		}	
		
		$row = DB::run("SELECT datatype_id, required, multiselect FROM `parametr` WHERE id=?", [$param_id])->fetch();		
		return $obj->GetField($row['datatype_id'],$param_id,$row['multiselect'],$row['required'],null,$value);
	}
	
	//удаляем данные 
	function DeleteData(){
		if(Helper::UserDataAdd($entity) == $this->user_session or $_SESSION['user_role']==1){
			$param_id = route::arg();
			$entity = $_POST['entity'];
		
			if(Helper::IsParamRequired($param_id)==0){
				$data_type = $_POST['data_type'];
				
				if($data_type == 'discrete' || $data_type == 'multidiscrete'){
					DB::run("DELETE FROM `data_discrete` WHERE parametr_id=? AND entity_id=?", [$param_id,$entity]); 
				}
				elseif($data_type == 'file' || $data_type == 'img'){
					$obj = new Files('input');
					$filename = $entity.'_'.$param_id;
					$obj ->FileDelete($filename);
				}
				else
					DB::run("DELETE FROM `data` WHERE parametr_id =? AND entity_id=?", [$param_id,$entity]);
			}
		}
	}
	
	//редактируем данные
	function EditData(){
		$entity = $_POST['entity'];	
		if(Helper::UserDataAdd($entity) == $this->user_session or $_SESSION['user_role']==1){

			$param_id = route::arg();
			$data_type = $_POST['data_type'];
			$value = $_POST['value'] ?? null;			
			//валидация
			if($data_type == 'datetime'){
				if(!Helper::validDate($value)) echo 'неверный формат даты';
				die;
			}
			
			if($data_type == 'file' || $data_type == 'img'){
				if($_FILES){
					$filename = $entity.'_'.$param_id;
					$obj = new Files('input');
					$obj -> FileUpload($filename);
				}			
			}			
			elseif($data_type == 'discrete')
				DB::run("UPDATE `data_discrete` SET discrete_id=? WHERE parametr_id=? AND entity_id=?", [$value,$param_id,$entity]);
			
			elseif($data_type == 'multidiscrete'){
				DB::run("DELETE FROM `data_discrete` WHERE parametr_id=? AND entity_id=?", [$param_id,$entity]);
				if(is_array($value)){ 						
					$stmt = DB::prepare("INSERT INTO `data_discrete` (`discrete_id`,`entity_id`,`parametr_id`) VALUES (?,?,?)");
					foreach ($value as $discrete_id)
						$stmt->execute([$discrete_id, $entity, $param_id]);
				}
			}
			else{
				if($data_type == 'float')
					$value = str_replace(',' , '.' , $value );
				
				if($data_type == 'datetime')
					$value = Helper::ChangeDateFormat($value,'Y-m-d');
				
				$data_field = $data_type.'_value';
			
				DB::run("UPDATE `data` SET $data_field=? WHERE parametr_id=? AND entity_id=?", [$value,$param_id,$entity]);
			}
		}
	}
	
	/** вытаскиваем все данные конкретной сущности */
	public function GetEntity(){
		
		//if(Helper::UserDataAdd(route::arg()) == $this->user_session or $_SESSION['user_role']==1){	
		
			$entity_id = route::arg();
			
			//из таблицы data
			$stmt = DB::run("SELECT * FROM data WHERE entity_id = ?",[$entity_id]);			
			while ($row = $stmt->fetch()){
				
				$param_id = $row['parametr_id'];
				$access = helper::IsParamAccess($param_id);
				if($access == 0 && Helper::UserDataAdd(route::arg()) != $this->user_session && $_SESSION['user_role']!=1)
					continue;
				
				$param_name = Helper::ParamName($param_id);
				$category = Helper::CategoryNameParamId($param_id);
				$unit = Helper::UnitNameParamId($param_id);
				$datatype = Helper::DataTypeEName($param_id);
				$field = $datatype.'_value';
				$data = $row[$field];
				$data = $datatype=='logical' ? Helper::LogicalArray()[$data] : $data;
				$data = $datatype=='datetime' ? Helper::ChangeDateFormat($data,'d.m.Y') : $data;
				
				$data_array[$category][$param_name] = array($data,$unit,$param_id,$entity_id,$datatype); 
			}
			
			//из таблицы data_discrete (дискретные проверяем)
			$stmt = DB::run("
			SELECT t2.id, t2.name as value, t3.name as param_name, t4.name as category, t2.parametr_id 
			FROM data_discrete t1, discrete t2, parametr t3, category t4
			WHERE 
				t1.entity_id = ? 
				AND t1.discrete_id = t2.id 
				AND t2.parametr_id=t3.id 
				AND t3.category_id=t4.id"
			,[$entity_id]);
			
			while ($row = $stmt->fetch()){
				//$discrete_id[] = $row['id'];
				$category = $row['category'];
				$param_id = $row['parametr_id'];
				$access = helper::IsParamAccess($param_id);
				if($access == 0 && Helper::UserDataAdd(route::arg()) != $this->user_session && $_SESSION['user_role']!=1)
					continue;
				$param_name = $row['param_name'];				
				$value[$param_name][] = $row['value'];
				
				if(isset($data_array[$category][$param_name]))					
					$data_array[$category][$param_name] = array(implode(', ',$value[$param_name]),false,$param_id,$entity_id,'multidiscrete');
				else
					$data_array[$category][$param_name] = array($row['value'],false,$param_id,$entity_id,'discrete'); 
			}
				
			//проверяем наличие изображений и файлов
			$obj = new files('input');			
			$files_arr = $obj ->FileEntity($entity_id);
			if(count($files_arr)){
				foreach ($files_arr as $path =>$param_id){
					$access = helper::IsParamAccess($param_id);
					if($access == 0 && Helper::UserDataAdd(route::arg()) != $this->user_session && $_SESSION['user_role']!=1)
						continue;
					$category = Helper::CategoryNameParamId($param_id);
					$param_name = Helper::ParamName($param_id);
					$datatype = Helper::DataTypeEName($param_id);
					
					switch ($datatype){
						case 'img':
							$link = '<a href="/'.$path.'" data-fancybox><img src="/'.$path.'"></a>';break;
						case 'file':
							$link = '<a href="/'.$path.'" target=_blank>посмотреть файл</a>';break;
					}
					
					$data_array[$category][$param_name] = array($link,false,$param_id,$entity_id,$datatype); 
				}
			}
			
			//print_r('<pre>');var_dump($data_array);print_r('</pre>');
			//die;
			return $data_array;
		//}
		//else
			//route::ErrorPage404();
	}
}