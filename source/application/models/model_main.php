<?
class Model_Main extends Model{

	public function GetParam(){
		include 'application/models/model_input.php';
		$obj = new Model_Input();
		//возвращаем массив с параметрами, исключив из него параметры с типами данных "изображение" и "файл"
		$arr = $obj->GetAllParam('WHERE t1.datatype_id NOT IN (7,9)');
		
		$gost_array['ГОСТ/Метод'][] = ['gost','ГОСТ', 0];
		$gost_array['ГОСТ/Метод'][] = ['method', 'Метод испытания', 0];
		
		$newarr = array_merge_recursive($arr,$gost_array);
		ksort($newarr);	
		
		//Выделяем те параметры, по которым есть значения в БД
		$param_is_data = db::run("SELECT Distinct parametr_id FROM `data` UNION SELECT Distinct parametr_id FROM `data_discrete` ORDER BY parametr_id ASC")->fetchAll(PDO::FETCH_COLUMN);
		
		foreach($newarr as &$category)
			foreach($category as &$array)
				if (in_array($array[0],$param_is_data) or in_array($array[0],$gost_array['ГОСТ/Метод'][0]))
					$array[1] = $array[1].'@';
		
		return $newarr;
	}
}


