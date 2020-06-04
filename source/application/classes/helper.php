<?
class Helper{

	/** Изменить формат даты 23.12.2016 => 2016-12-23  или наоборот */
	static function ChangeDateFormat($sourceDate, $newFormat) {
		if(!empty($sourceDate) && $sourceDate!='0000-00-00')
			return date($newFormat, strtotime($sourceDate));
	}
	
	/** Список значений дискретного параметра */
	static function DiscreteValueList($paramid){
		return DB::run("SELECT `id`, `name` FROM `discrete` WHERE `parametr_id`=? ORDER BY `order` ASC, name ASC",[$paramid])->fetchAll(PDO::FETCH_KEY_PAIR);
	} 
	
	/** мультиселект или нет */
	static function IsMultiSelect($paramid){
		return DB::run("SELECT `multiselect` FROM `parametr` WHERE id=?",[$paramid])->fetchColumn();
	}
	
	/** Английское название типа данных по id параметра */
    static function DataTypeEName($paramid){
	   return DB::run("SELECT `ename` FROM `parametr` t1, `datatype` t2 WHERE t2.id = t1.datatype_id AND t1.id = ?", [$paramid])->fetchColumn();
    }
	
	//id типа данных по id параметра */
    static function DataTypeID($paramid){
	   return DB::run("SELECT t2.`id` FROM `parametr` t1, `datatype` t2 WHERE t2.id = t1.datatype_id AND t1.id = ?", [$paramid])->fetchColumn();
    }
	
	/** type типа данных по id параметра */
    static function DataTypeType($paramid){
	   return DB::run("SELECT t2.`type` FROM `parametr` t1, `datatype` t2 WHERE t2.id = t1.datatype_id AND t1.id = ?", [$paramid])->fetchColumn();
    }
   
    /** название параметра по id */
    static function ParamName($id){
	   return DB::run("SELECT `name` FROM `parametr` WHERE id = ?", [$id])->fetchColumn();
    }
	
	/** название параметра вместе с единицей измерения (по id) */
    static function ParamNameUnit($paramid){		
	   $row = DB::run("SELECT parametr.name as pname, unit.name as uname FROM `parametr` LEFT JOIN unit ON unit.id=parametr.unit_id WHERE parametr.id = ?", [$paramid])->fetch();
	   $paramname = $row['pname'];
	   $unit = $row['uname'];
	   if($unit)
		   $paramname .= ', '.$unit;
	   return $paramname;
    }
	
	/** название метода по id */
    static function MethodName($id){
	   return DB::run("SELECT `name` FROM `method` WHERE id = ?", [$id])->fetchColumn();
    }
	
	/** название Госта по id метода */
    static function GostNameMethodId($id){
	   return DB::run("SELECT gost.name FROM gost, method WHERE method.id=? AND method.gost_id = gost.id", [$id])->fetchColumn();
    }
	
	/** список всех гостов */
    static function GetGost(){
        return DB::run("SELECT `id`, `name` FROM `gost` ORDER BY `name`")->fetchAll(PDO::FETCH_KEY_PAIR);
    }
    
    /** список методов определенного госта */
    static function GetMethod($gost_id){
        return DB::run("SELECT `id`, `name` FROM `method` WHERE `gost_id`=? ORDER BY `name`",[$gost_id])->fetchAll(PDO::FETCH_KEY_PAIR);
    }
	
	/** список всех методов группами по гостам */
	static function getArrayGost(){
        $stmt = DB::run("SELECT t1.id, t1.name as mname, t2.name as gname FROM `method` t1, `gost` t2 WHERE t2.id=gost_id");
        while ($row = $stmt->fetch()){
            $gname = $row['gname'];
			$explode = explode(' ',$gname);
			$gname = $explode[1] ? $explode[1] : $explode[0];
            $mid = $row['id'];
            $opt[$gname][$mid] = $row['mname'];
        }
        //var_dump($opt);
		return $opt;
    } 

	/** название госта и метода по id сущности (entity_id) */
	static function GostMethodNameEntityId($entity_id){
		$row = db::run("SELECT t3.name as g_name, t2.name as m_name FROM entity t1, method t2, gost t3 
						WHERE t2.id = t1.method_id AND t2.gost_id = t3.id AND t1.id = ?",[$entity_id])->fetch();
		return $row['g_name'].'<br>МЕТОД '.$row['m_name'];
	}
		
	/** название категории по id параметра */
	static function CategoryNameParamId($id){
		return DB::run("SELECT category.name FROM parametr,category WHERE parametr.id=? AND category.id=parametr.category_id",[$id])->fetchColumn();
	}
	
	/** название единицы измерения по id параметра */
	static function UnitNameParamId($id){
		return DB::run("SELECT unit.name FROM parametr,unit WHERE parametr.id=? AND unit.id=parametr.unit_id",[$id])->fetchColumn();
	}
	
	/** массив логического типа данных */
	static function LogicalArray(){
		return [1=>'ДА',0=>'НЕТ'];
	}
	
	/** получаем массив параметров со статусом "ДА" в поле pattern (поле "шаблон" в структуре-параметры) */
	static function GetParamPattern(){
		return $pattern_array = DB::run("SELECT `id`,`name` FROM `parametr` WHERE pattern=1")->fetchAll(PDO::FETCH_KEY_PAIR);
	}
	
	/** получаем массив параметров со статусом "ДА" в поле search (поле будет столбцом в таблице результатов быстрого поиска) */
	static function GetParamSearch(){
		return $pattern_array = DB::run("SELECT `id`,`name` FROM `parametr` WHERE search=1")->fetchAll(PDO::FETCH_KEY_PAIR);
	}
	
	/** Обязателен ли параметр для заполнения	*/
	static function IsParamRequired($param_id){
		return DB::run("SELECT `required` FROM `parametr` WHERE id = ?", [$param_id])->fetchColumn();
	}
	
	/** Доступ к параметру (виден всем или не виден)	*/
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
	
	/** проверка на правильность формата даты */
	static function validDate($date) { 
		$d = DateTime::createFromFormat('d.m.Y', $date);	
		return $d && $d->format('d.m.Y') === $date;
	}
	
	/** удаление из многомерного массива $array повторяющихся значений по определенному ключу $key */
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
	
	/** Поиск значения по ключу в многомерном массиве */
	static function SearchMultiArray($array, $type){
		$jsonIterator = new RecursiveIteratorIterator(new RecursiveArrayIterator($array),RecursiveIteratorIterator::SELF_FIRST);
		foreach ($jsonIterator as $key => $val){
			if($key == $type && !is_array($val))
				return $val;
		}
		return false;
	}
	
	/** Логирование */
	static function WriteLog($name,$entity = ''){		
		$ip=$_SERVER['REMOTE_ADDR'];
		$date = date('d.m.Y H:i:s');		
		$user = $_SESSION['user_name'] ?? 'unknown';
		//unknown
		// debug_backtrace()[1]['function'] - показывает имя метода, в котором вызывается LogChanges
		
		$str = $date.' '.$ip.' '.$user.' '.$_SERVER['REQUEST_URI'].' '.$entity. "\r\n";
		//file_put_contents('logs/log_changes.txt',$str, FILE_APPEND);
		file_put_contents('logs/log_'.$name.'.txt',$str, FILE_APPEND);
		
	}
	
}
?>