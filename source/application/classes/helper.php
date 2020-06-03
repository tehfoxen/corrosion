<?
class Helper{

/** Изменить формат даты 23.12.2016 => 2016-12-23  или наоборот */
	static function ChangeDateFormat($sourceDate, $newFormat) {
		if(!empty($sourceDate) && $sourceDate!='0000-00-00')
			return date($newFormat, strtotime($sourceDate));
	}
	
	//Английское название типа данных по id параметра
    static function DataTypeEName($paramid){
	   return DB::run("SELECT `ename` FROM `parametr` t1, `datatype` t2 WHERE t2.id = t1.datatype_id AND t1.id = ?", [$paramid])->fetchColumn();
    }
   
    //название параметра по id
    static function ParamName($id){
	   return DB::run("SELECT `name` FROM `parametr` WHERE id = ?", [$id])->fetchColumn();
    }
	
	//название метода по id
    static function MethodName($id){
	   return DB::run("SELECT `name` FROM `method` WHERE id = ?", [$id])->fetchColumn();
    }
	
	//название Госта по id метода
    static function GostNameMethodId($id){
	   return DB::run("SELECT gost.name FROM gost, method WHERE method.id=? AND method.gost_id = gost.id", [$id])->fetchColumn();
    }
	
	// название категории по id параметра
	static function CategoryNameParamId($id){
		return DB::run("SELECT category.name FROM parametr,category WHERE parametr.id=? AND category.id=parametr.category_id",[$id])->fetchColumn();
	}
	
	// название единицы измерения по id параметра
	static function UnitNameParamId($id){
		return DB::run("SELECT unit.name FROM parametr,unit WHERE parametr.id=? AND unit.id=parametr.unit_id",[$id])->fetchColumn();
	}
	
	//массив логического типа данных
	static function LogicalArray(){
		return [1=>'ДА',0=>'НЕТ'];
	}
	
	// получаем массив параметров со статусом "ДА" в поле pattern (поле "шаблон" в структуре-параметры)
	public function GetParamPattern(){
		return $pattern_array = DB::run("SELECT `id`,`name` FROM `parametr` WHERE pattern=1")->fetchAll(PDO::FETCH_KEY_PAIR);
	}
	
	// получаем массив параметров со статусом "ДА" в поле search (поле будет столбцом в таблице результатов быстрого поиска)
	public function GetParamSearch(){
		return $pattern_array = DB::run("SELECT `id`,`name` FROM `parametr` WHERE search=1")->fetchAll(PDO::FETCH_KEY_PAIR);
	}
	
	//Обязателен ли параметр для заполнения	
	static function IsParamRequired($param_id){
		return DB::run("SELECT `required` FROM `parametr` WHERE id = ?", [$param_id])->fetchColumn();
	}
	
	//Доступ к параметру (виден всем или не виден)	
	static function IsParamAccess($param_id){
		return DB::run("SELECT `access` FROM `parametr` WHERE id = ?", [$param_id])->fetchColumn();
	}
	
	/** ID юзера, который добавил сущность */
	static function UserDataAdd($entity_id){
		return DB::run("SELECT user_id FROM entity WHERE id=?",[$entity_id])->fetchColumn();
	}
	
	/** Имя юзера по ID */
	static function UserName($user_id){
		return DB::run("SELECT `name` FROM user WHERE id=?",[$user_id])->fetchColumn();
	}
	
	// проверка на правильность формата даты
	static function validDate($date) { 
		$d = DateTime::createFromFormat('d.m.Y', $date);	
		return $d && $d->format('d.m.Y') === $date;
	}
	
	//удаление из многомерного массива $array повторяющихся значений по определенному ключу $key
	static function array_unique_key($array, $key) { 
			$tmp = $key_array = array(); 
			$i = 0; 		 
			foreach($array as $k=>$val) { 
				if (!in_array($val[$key], $key_array)) { 
					$key_array[$i] = $val[$key]; 
					$tmp[$k] = $val; 
				} 
				$i++; 
			} 
			return $tmp; 
	}
	
	
	
}
?>