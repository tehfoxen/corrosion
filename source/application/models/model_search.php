<?
class Model_Search extends Model{

	function __construct(){		
        //$this->user_session = $_SESSION['user_id'];	
		$this->query_arr = [1=>'существует', 2=>'не существует', 3=>'равно', 4=>'не равно', 5=>'содержит', 6=>'как минимум', 7=>'не более', 8=>'между', 9=>'одно из'];		
	} 
	
	//вывод уточняющего селекта
/**
1 число целое integer
2 число с точкой float
3 дата datetime
4 логический logical
5 короткий текст shorttext
6 длинный текст longtext
8 дискретный discrete
*/	

	//выбор подзапроса в зависимости от типа данных параметра
	function ChooseQuery($datatype){
		$arr = $this->query_arr;

		switch ($datatype) {
                case 1: 
				case 2:
				case 3:
					unset($arr[4],$arr[5],$arr[9]);break;
					//$arr[] = array('является', 'как минимум', 'не более', 'между');break;
					
                case 4:
					unset($arr[4],$arr[5],$arr[6],$arr[7],$arr[8],$arr[9]);break;
					//$arr[] = array('является');break;
                case 5:
				case 6:
					unset($arr[3],$arr[4],$arr[6],$arr[7],$arr[8],$arr[9]);break;
					//$arr[] = array('содержит');break;
				case 8:
					unset($arr[5],$arr[6],$arr[7],$arr[8]);break;
        }
        return $arr;
	}
	
	
	/** для каждого параметра получаем селект существует,не существует, равно и пр...*/
	function GetSelectQuery(){
		foreach ($_POST['choose_param'] as $paramid){
			$datatype =  helper::DataTypeID($paramid);
			$param = helper::ParamNameUnit($paramid);
			$minmax = '';
			
			if($paramid == 'gost'){
				$datatype = 8;
				$param = 'ГОСТ';
			}
			if($paramid == 'method'){
				$datatype = 8;
				$param = 'Метод Испытания';
			}
			
			//ищем минимальное и максимальное значения параметра в БД
			if(in_array($datatype,[1,2,3])){
				$datatype_name = helper::DataTypeEName($paramid);
				$field = $datatype_name.'_value';
				$row = DB::run("SELECT MIN($field) as `min`, MAX($field) as `max` FROM `data` WHERE parametr_id=?",[$paramid])->fetch();
				$min = $row['min'];
				$max = $row['max'];
				if($datatype == 3){
					$min = Helper::ChangeDateFormat($min, 'd.m.Y');
					$max = Helper::ChangeDateFormat($max, 'd.m.Y');
				}
				else{
					$min = round($min,4);
					$max = round($max,4);
				}
				$minmax = '  <span class=minmax>['.$min.' ... '.$max.']</span>';
			}
			
			if($minmax)
				$param .= $minmax;
			
			$arr[$paramid][$param] = $this->ChooseQuery($datatype);
		}
		//var_dump($arr);
		return $arr;
	}
	
	/** для каждого параметра генерируем поле для ввода значения в засисимости от того, какой селект (существует, не существует, равно и пр.) был выбран 
	1 - существует
	2 - не существует
	3 - равно
	4 - не равно
	5 - содержит
	6 - как минимум
	7 - не более
	8 - между
	9 - одно из	
	*/
	function GetQueryField($paramid){
		include 'application/models/model_input.php';
		$obj = new Model_Input();
		$select = route::arg();
		//echo '$select='.$select.'$paramid='.$paramid;
		if($select !=1 and $select !=2){				
						
			$datatype = helper::DataTypeID($paramid);
			$fieldname = 'param_'.$param_id;
			$multiselect = helper::IsMultiSelect($paramid);
			
			if($paramid == 'gost' or $paramid == 'method'){
				$datatype = 8;
				$multiselect = 0;
			}
			
			if($select == 9)
				$multiselect = 1;
			$required = 1;
			$title = '';	
			$field = $obj->GetField($datatype,$paramid,$multiselect,$required,$title);			
			if($select == 8){
				$field = $obj->GetField($datatype,$paramid.'[]',$multiselect,$required,$title);
				$field = $field.' и '.$field;
			}
			
			//echo $field;
			return $field;
		}
	}
	
	/** формирование строки запроса к БД в зависимости от выбора (существует, не существует и пр.)*/
	function SQLQuery($query, $value){
		if(is_array($value))
			$value_str = implode(',',$value);
		
		switch ($query){
			case 1:
				$where = 'IS NOT NULL'; break;
			case 2:
				$where = 'IS NULL'; break;
			case 3:
				$where = " = '$value'"; 
				if(is_array($value)) 
					$where = "IN ($value_str)"; 
				break;
			case 4:
				$where = "!= '$value'"; 
				if(is_array($value))
					$where = "NOT IN ($value_str)"; 
				break;
			case 5:
				$where = "LIKE '%$value%'"; break;
			case 6:
				$where = ">= '$value'"; break;
			case 7:
				$where = "<= '$value'"; break;
			case 8:
				$where = "BETWEEN '$value[0]' AND '$value[1]'"; break;
			case 9:
				$where = "IN ($value)"; 
				if(is_array($value))
					$where = "IN ($value_str)"; 
				break;
		}
		return $where;
	}
	/**
	Поиск по параметрам.
	Алгоритм:
	1. получаем в массиве POST условие для поиска (существует, больше, меньше и т.д.) и параметр со значением
	2. преобразуем это всё в удобный для дальнейшей работы массив из элементов, равных id параметра
	3. По каждому параметру из поиска ищем в БД данные согласно заданному условию. Получаем массив id-сущностей
	4. Сравниваем все массивы сущностей по всем параметрам и составляем массив сущностей, которые присутвуют во всех массивах одновременно. Таким образом мы соблюдаем ключевое условие поиска "И"
	5. Ищем по каждому параметру заданное в поиске значение с учетом ограничения по сущностям из п.4
	6. Создаем финальный массив с данными и сортируем его	
	*/
	function SearchResultParam(){
		$arr_value = [];
		foreach ($_POST as $k=>$v){
			$explode = explode('_',$k);
			$paramid = $explode[1];
			$name = $explode[0];
			$arr[$paramid][$name] = $v;
		}
		/* echo '====== $arr =====';
		var_dump($arr);
		echo '==========='; */
		
		foreach ($arr as $paramid=>$v){
			$query = $v['query'];
			$value = $v['param'];
			$where = $this->SQLQuery($query,$value);
			
			if($paramid == 'gost') {
								
				$method_array = DB::run("SELECT `id` FROM `method` WHERE gost_id $where")->fetchAll(PDO::FETCH_COLUMN);
				$method_str = implode(',',$method_array);
				$arr_entity[] = DB::run("SELECT id FROM `entity` WHERE method_id IN ($method_str)")->fetchAll(PDO::FETCH_COLUMN);
				
				$arr_query_select[$paramid] = "SELECT `name` FROM `gost` WHERE `id` $where";
			}
			elseif($paramid == 'method'){
				
				$arr_entity[] = DB::run("SELECT id FROM `entity` WHERE method_id $where")->fetchAll(PDO::FETCH_COLUMN);
				
				$arr_query_select[$paramid] = "SELECT `name` FROM `method` WHERE `id` $where";
			}
			else{		
				$datatype = helper::DataTypeEName($paramid);							
				$table_name = 'data';
				$tablefield = $datatype.'_value';					
				
				if($datatype == 'discrete'){
					$tablefield = 'discrete_id';
					$table_name = 'data_discrete';
				}
				
				if($datatype == 'datetime'){
					if($query == 8)
						foreach ($value as $key=>&$val)
							$val = Helper::ChangeDateFormat($val,'Y-m-d');
					else
						$value = Helper::ChangeDateFormat($value,'Y-m-d');
				}
				
				if($datatype == 'float'){
					$value = str_replace(',' , '.' , $value );					
				} 
				
				if($query == 8){
					$value_copy = $value;
					$value[0] = min($value_copy);
					$value[1] = max($value_copy);
				}
				
				$where = $this->SQLQuery($query,$value);				
				
				//$where = $this->SQLQuery($query,$tablefield,$value,$paramid);
				$where = "t1.parametr_id = $paramid AND $tablefield ".$where;
				//echo $where;
				
				$query_select = "SELECT entity_id FROM `$table_name` t1 WHERE $where";
				$query_select2 = "SELECT $tablefield FROM `$table_name` t1 WHERE $where";
				if($datatype == 'discrete')
					$query_select2 = "SELECT t2.name FROM `discrete` t2, `$table_name` t1 WHERE $where AND t2.id=t1.discrete_id";
							
				$arr_query_select[$paramid] = $query_select2 ;			
				$arr_entity[] = DB::run($query_select)->fetchAll(PDO::FETCH_COLUMN);
			}
			
		}
		
		/* echo '======== arr_entity  ==============';
		var_dump($arr_entity);
		echo '========================'; */
		
	
		//если в поиске несколько параметров, сравниваем массивы с id-сущностей и создаем такой, который состоит из значений, которые есть во всех массивах. 
		$arr_entity_result = count($arr_entity)>1 ? call_user_func_array('array_intersect', $arr_entity) : $arr_entity[0];
		
		/* echo '======== arr_entity_result  ==============';
		var_dump($arr_entity_result); 
		echo '========================'; */
		
		/* echo '======== arr_query_select  ==============';
		var_dump($arr_query_select);
		echo '========================'; */
		
		sort($arr_entity_result);
		
		/* echo '======== sort arr_entity_result  ==============';
		var_dump($arr_entity_result); 
		echo '========================'; */
		
		$entity_str = implode(',',$arr_entity_result);
		
		//создаем финальный массив с данными
		if($entity_str){
			foreach($arr_query_select as $paramid=>$select){	
				if($paramid == 'gost'){
					foreach ($arr_entity_result as $entity_id){
						$gost_name = db::run("SELECT t1.name FROM `gost`t1
											LEFT JOIN `method` t2 ON (t2.gost_id = t1.id)
											LEFT JOIN `entity` t3 ON (t3.method_id = t2.id)
											WHERE t3.id = $entity_id")->fetchColumn();
						
						$arr_value[$paramid][] = $gost_name;
					}
				}					
				elseif($paramid == 'method'){
					foreach ($arr_entity_result as $entity_id){
						$method_name = db::run("SELECT t1.name FROM `method`t1
											LEFT JOIN `entity` t2 ON (t2.method_id = t1.id)
											WHERE t2.id = $entity_id")->fetchColumn();
						
						$arr_value[$paramid][] = $method_name;
					}
				}
				else{
					$arr_value[$paramid] = DB::run($select."  AND entity_id IN ($entity_str) ORDER BY entity_id ASC")->fetchAll(PDO::FETCH_COLUMN);
					
					//если даты - преобразуем в нормальный вид
					/* $datatype = helper::DataTypeEName($paramid);
					if($datatype == 'datetime'){
						foreach ($arr_value[$paramid] as $k=>&$v)						
							$v = helper::ChangeDateFormat($v,'d.m.Y');	
					} */
				}
					
			}
			$arr_value['entity_id'] = $arr_entity_result;
			
			/* echo '======== arr_value  =========';
			var_dump($arr_value);
			echo '========================'; */
			
			//чтобы корректно работал call_user_func_array с array_multisort - нужно передать массив параметров, который является ссылкой на основной массив.
			
			foreach($arr_value as &$val){
				$params[] = &$val;
			}
			
			call_user_func_array('array_multisort', $params);
			
			/* echo '======== arr_value после сортировки  ==============';
			var_dump($arr_value);
			echo '========================'; */
			
			//пробразуем даты в массиве в удобочитаемые даты. Важно это делать после сортировки. формат даты dd.mm.yyyy как-то криво сортируется.
			foreach($arr_value as $key=>&$val){
				if(is_int($key)){
					$datatype = helper::DataTypeEName($key);
					if($datatype == 'datetime'){
						foreach ($val as $k=>&$v)						
							$v = helper::ChangeDateFormat($v,'d.m.Y');	
					}
				} 					
			} 
			
			/* echo '======== arr_value после преобразования дат  ==============';
			var_dump($arr_value);
			echo '========================'; */
			
			
			//преобразуем массив в удобный для вывода в таблице
			$n = 0;			
			foreach ($arr_value as $key=>$value){
				switch ($key){
					case 'entity_id':
						$title = 'entity_id'; break;
					case 'gost':
						$title = 'ГОСТ'; break;
					case 'method':
						$title = 'Метод испытания';	break;	
					default:												
						$title = $key.'@'.helper::ParamNameUnit($key);
				}
				
				$result['title'][] = $title;
				
				foreach ($value as $k=>$v){
					$result[$n][] = $value[$n];
					$n++;
				}
				$n = 0;
			}
	
			/* echo '======== search_result  ==============';
			var_dump($result);
			echo '========================'; */
			
		}
				
		$search = count($result) ? $result : 'Ничего не найдено';
		
		return $search;
	}
}	



