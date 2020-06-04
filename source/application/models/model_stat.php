<?
class Model_Stat extends Model{
	function GetStatSelect(){
		//var_dump($_POST['paramid_array']);
		$str = '';
		$isval = 0;
		$stat_method_array = ['1'=> 'Среднее арифметическое','Медиана','Стандартное отклонение','Минимальное значение','Максимальное значение'];
		include 'application/classes/fieldsform.php';
		$obj = new FieldsForm();		
		
		foreach ($_POST['paramid_array'] as $paramid){
			$attr = '';
			if ($paramid == 0)
				$value = '';
			else{
				$datatype_id = helper::DataTypeID($paramid);
				if($datatype_id == 1 or $datatype_id == 2)
					$value = $obj->SelectMultiple($stat_method_array,'param_'.$paramid, 6, ['title'=>'','class'=>'stat_method']);
				else
					$value = '';
			}
			
			if($value){
				$isval = 1;
				$attr = 'id='.$paramid;
			}
				
			$str .= '<td '.$attr.'>'.$value.'</td>';
		}
		if ($isval)
			return $str;
	}
	
	function GetStatResult(){
		$result = [];
		//var_dump($_POST['stat_array']);
		foreach($_POST['stat_array'] as $paramid=>$array){
			$data = $array;
			unset ($data[0]);
			//var_dump($data);
			
			foreach($array[0] as $key=>$method_id){
				
				switch ($method_id) {
					//Среднее арифметическое
					case 1: 
						$result[$paramid][$method_id] = round($this->average($data),4);break;
					
					//Медиана
					case 2:
						$result[$paramid][$method_id] = round($this->median($data),4);break;
						
					//Стандартное отклонение
					case 3:																		
						//$result[$paramid][$method] = stats_standard_deviation($data); break;
						$result[$paramid][$method_id] = round($this->standard_deviation($data),4); break;
					
					// минимальное
					case 4:						
						$result[$paramid][$method_id] = round(min($data),4); break;
					
					//максимальное
					case 5:
						$result[$paramid][$method_id] = round(max($data),4); break;
				}
			}
		}
		//var_dump($result);
		return $result;
	}
	/** Среднее арифметическое */
	function average($array){
		return array_sum($array)/count($array);
	}
	
	/** Медиана */
	function median($array){
		sort($array);
		$count = count($array);
		$num = $count/2;
		$fmod = fmod($count,2);
		if($fmod == 1){
			$i = floor($num);
			$median = $array[$i];
		}
		else
			$median = ($array[$num-1]+$array[$num])/2;
		return $median;
	}
	
	/** Стандартное отклонение https://yourtutor.info/среднеквадратическое-отклонение */
	function standard_deviation($array){
		$average = $this->average($array);
		foreach ($array as $val)		
			$deviation[] = $val-$average;
		foreach ($deviation as $val)		
			$square[] = pow($val,2);
		$dispersion = array_sum($square)/(count($square)-1);
		return sqrt($dispersion); 				
	}
	
}