<?
class Model_Mydata extends Model{
    
    function __construct(){		
        $this->user_session = $_SESSION['user_id'];
	} 
    
	/** получаем массив данных юзера для отображения таблицы в разделе "мои данные" */
	public function GetMyData($userid = NULL){		
		$user_id = $userid ? $userid : $this->user_session;
		
		$stmt = DB::run("SELECT create_time, id, method_id FROM entity WHERE user_id=?",[$user_id]);
		$entity_id_array = $mydata = [];
		
		while ($row = $stmt->fetch()){
			$entity_id = $row['id'];
			$entity_id_array[] = $entity_id;
			$entity_method_array[$entity_id] = $row['method_id'];
			$entity_time_array[$entity_id] = Helper::ChangeDateFormat($row['create_time'],'d.m.Y H:i');
		}
		if(count($entity_id_array)){
		
			$entity_str = implode(',',$entity_id_array);
			//собираем в массив поля, помеченные как "шаблон"
			$pattern_key = array_keys(Helper::GetParamPattern());			
			$pattern_str = implode(',',$pattern_key);
						
			//Массив из обычных данных (таблица data)
			$stmt = DB::run("SELECT * FROM data WHERE entity_id IN ($entity_str) AND parametr_id IN ($pattern_str) ORDER BY entity_id DESC, parametr_id ASC");
			//echo "SELECT * FROM data WHERE entity_id IN ($entity_str) AND parametr_id IN ($pattern_str) ORDER BY entity_id DESC, parametr_id ASC";
			while ($row = $stmt->fetch()){
				$param_id = $row['parametr_id'];
				$param_name = Helper::ParamName($param_id);			
				$entity_id = $row['entity_id'];
				$method_id = $entity_method_array[$entity_id];
				$time_create = $entity_time_array[$entity_id];
				$datatype = Helper::DataTypeEName($param_id);
				$field = $datatype.'_value';

				$mydata[$entity_id]['Дата внесения'] = $time_create;
				//$mydata[$entity_id]['ГОСТ'] = Helper::GostNameMethodId($method_id);
				//$mydata[$entity_id]['Метод испытания'] = Helper::MethodName($method_id);
				
				//'Описание испытания' - параметр, добавленный в Структура-Параметры. Сделан полем "Шаблон" и "Обязательным". Но для испытаний по ГОСТ параметр удаляется с помощью JS. В следующий массив добавляется либо гост.метод, либо значение параметра 'Описание испытания'.
				$mydata[$entity_id]['Описание испытания'] = Helper::GostNameMethodId($method_id).'<div data-method='.$method_id.'>'.Helper::MethodName($method_id).'</div>';
				
				$entity_arr[$entity_id] = $entity_id;			
				
				if(isset($row[$field])){
					if($datatype == 'datetime')
						$row[$field] =  Helper::ChangeDateFormat($row[$field],'d.m.Y');					
					$mydata[$entity_id][$param_name] = $row[$field];
				}
			}
			
			// массив из дискретных данных
			$stmt = DB::run("SELECT t1.entity_id, t2.name as value, t3.name as param_name FROM data_discrete t1 LEFT JOIN discrete t2 ON t1.discrete_id = t2.id LEFT JOIN parametr t3 ON t1.parametr_id = t3.id WHERE t1.entity_id IN ($entity_str) AND t1.parametr_id IN ($pattern_str)");
			while ($row = $stmt->fetch()){
				$entity_id = $row['entity_id'];
				$param_name = $row['param_name'];
				$mydata[$entity_id][$param_name] = $row['value'];
			}
			
			foreach($entity_arr as $entity){
				foreach (Helper::GetParamPattern() as $k=>$v){
					if(!array_key_exists($v,$mydata[$entity]))	
						$mydata[$entity][$v] = '';
				}
			}
		}
		//echo $this->user_session;
		/* if($this->user_session==1){
			print_r('<pre>');var_dump($mydata);print_r('</pre>');
		} */
		return $mydata;
	}
	
	/** Удаление сущности (всей инфы об образце) Удаляем файлы и из таблицы entity. Из таблиц с данными удалится само (CASCADE in foreign key)*/
	public function DelEntity(){
		if(Helper::UserDataAdd(route::arg()) == $this->user_session or $_SESSION['user_role']==1){
			$entity_id = route::arg();
			$stmt = DB::run("DELETE FROM entity WHERE id=?", [$entity_id]);
			$obj = new files('input');
			$obj ->FileDelete($entity_id.'_*');	
		}
		else
			route::ErrorPage404();
	}
}