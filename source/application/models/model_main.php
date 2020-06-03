<?
class Model_Main extends Model
{
    
	function __construct(){		
        $this->search = $_REQUEST['search'];
	} 
	
	/** выделяет искомую фразу в результатах поиска жирным*/
	public function MarkSearch($found){
		return str_ireplace($this->search,'<b>'.$this->search.'</b>',$found);
		//return strtr ($this->search , $found , '<b>'.$this->search.'</b>' );
	}
	
	public function SearchResult(){
		$arr = [];
		//$search = $_REQUEST['search'];
		$search_str = "'%$this->search%'";
		
		//ищем в таблице data
		$field_array = ['float_value','integer_value','datetime_value','shorttext_value','longtext_value'];
		$search_string = '';
		$n=0;
		foreach ($field_array as $val){
			$like = $val=='datetime_value' ? ' LIKE binary ' : ' LIKE ';//бинари было добавлено после того как не сработал запрос "д"
			$search_string .= $val.$like.$search_str; 
			$n++;
			if($n<count($field_array))
				$search_string .= ' OR ';
		}
		$stmt = DB::run("SELECT * FROM `data` WHERE $search_string");
		while ($row = $stmt->fetch()){
			$entity_id = $row['entity_id'];
			$param_id = $row['parametr_id'];
			$param_name = Helper::ParamName($param_id);
			
			//против сдвигов
			$entity_arr[$entity_id] = $entity_id;	
			
			foreach ($field_array as $val){
				$value = $row[$val];
				if($value)
					$arr[$entity_id]['search_result'][$param_name] = $this->MarkSearch($value);
			}
		}
		
		//ищем в дискретных
		$stmt = DB::run("SELECT t1.parametr_id, t1.entity_id, t2.name as value, t3.name as param_name FROM data_discrete t1  
						LEFT JOIN discrete t2 
						ON t1.discrete_id = t2.id
						LEFT JOIN parametr t3 
						ON t1.parametr_id = t3.id
						WHERE t2.name LIKE $search_str");
		
		while ($row = $stmt->fetch()){
			$entity_id = $row['entity_id'];
			$param_name = $row['param_name'];
			
			//против сдвигов
			$entity_arr[$entity_id] = $entity_id;
						
			$arr[$entity_id]['search_result'][$param_name] = $this->MarkSearch($row['value']);
		}
		
		//чтобы не было сдвигов из-за пустых
		/* foreach($entity_arr as $entity){
				foreach (Helper::GetParamSearch() as $k=>$v){
					if(!array_key_exists($v,$arr[$entity_id]))	
						$arr[$entity_id]['search_result'][$v] = '';
				}
		} */
		
		
		ksort($arr);
		
		foreach($arr as $entity_id=>$val){
			
			//поиск значений параметров-столбцов таблицы результатов поиска для конкретной сущности
			foreach (helper::GetParamSearch() as $param_id=>$param_name){
				$row = DB::run("SELECT t2.name as method_name, t3.name as gost_name 
								FROM entity t1 
								LEFT JOIN method t2 ON t1.method_id = t2.id 
								LEFT JOIN gost t3 ON t3.id = t2.gost_id 
								WHERE t1.id=$entity_id")->fetch();
				
				if($param_name == 'Описание испытания' and $row['method_name'])
					$arr[$entity_id]['title']['Описание испытания'] = $row['gost_name'].'<br>'.$row['method_name'];
				
				$data_type = helper::DataTypeEName($param_id);
				$field_name =$data_type.'_value';
				
				if($data_type == 'discrete')							
					$shablon_value = DB::run("SELECT t2.name FROM data_discrete t1, discrete t2 WHERE t1.entity_id=$entity_id AND t1.parametr_id=$param_id AND t1.discrete_id=t2.id")->fetchColumn();
				else
					$shablon_value = DB::run("SELECT $field_name FROM `data` WHERE entity_id=$entity_id AND parametr_id=$param_id")->fetchColumn();
				
				//print_r('<pre>');var_dump($shablon_value);print_r('</pre>');
				//if($shablon_value)
				$arr[$entity_id]['title'][$param_name]=$shablon_value;
			}
		}
		/* print_r('<pre>');
		var_dump($arr);
		print_r('</pre>'); */
		
		return $arr;
	}
}