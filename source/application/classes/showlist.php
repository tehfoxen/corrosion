<?
class ShowList extends Structure{
       
    /** ссылка на удаление записи */
	function DeleteLink($id){
        return '<a class="del_record" href="/'.$this->table_name.'/del/'.$id.'">Удалить</a>&nbsp;&nbsp;';
	}
	
	/** ссылка на изменение записи*/
	function ChangeLink($id){
			return '<a class="edit_record" href="/'.$this->table_name.'/edit/'.$id.'">Изменить</a>&nbsp;&nbsp;';
	}
    
    function getList($where=''){
		//чтобы $this->fields не менять, присваиваем его другому массиву
		$f_array = $this->fields;
		$f = array();
		$orderby = (array_key_exists('name',$f_array)) ? ' `name` ASC ' : ' `id` ' ;
		
		$n=1;
		$echo = '';
		
		//парсим название таблицы $f_array[0]
		$ttype =  explode('@',$f_array[0]);		
		if(isset($ttype[1]))
			$orderby = $ttype[1];		
		
		unset($f_array[0]);
		
		$echo .= '<table class="spr_table" id="'.$this->table_name.'"><thead><tr>';	
		$echo .= '<th>№пп</th>';
		
		//заголовок столбца таблицы
		foreach($f_array as $k=>$v){
			$ftype = explode('@',$v);
			
			$hidden = (isset($ftype[4]) and $ftype[4] == 'hidden') ? 1 : 0;
			
			if($hidden == 0)
				$echo .= '<th>'.$ftype[0].'</th>';	
		}
			$echo .= '<th class="edit_del_links tablesorter-noSort"></th>';
		
		$echo .= '</thead></tr>';
		
		$query = "SELECT * FROM `$this->table_name` $where ORDER BY $orderby";
		$rs = DB::query($query);
		while ($row = $rs->fetch())
		{
			$echo .= '<tr id="'.$row['id'].'">';
			$echo .= '<td>'.$n.'</td>';
			$n++;
			foreach($f_array as $k=>$v){
				$ftype = explode('@',$v);
                $val = isset($row[$k]) ? $row[$k] : '';
				
				//если select или autocomplite (внешний ключ), берем данные из соответствующей таблицы. 
				if($ftype[1]=='select' or $ftype[1]=='autocomplete'){
					$f = explode('_id',$k);
					if(count($f)==2) {
						$table = $f[0];					
                        $val = DB::query("SELECT `name` FROM `$table` WHERE `id`='$val'")->fetchColumn();
					}
				}
				//если selectmulti - внешний ключ - мультиплай
				if($ftype[1]=='selectmulti'){
					$arr_multi = array();
					$f = explode('_id',$k);
					if(count($f)==2) {
						$table = $f[0];	
						$val_multi = explode(',',$val);
						
						foreach ($val_multi as $multi){
                            $arr_multi[] = DB::query("SELECT `name` FROM `$table` WHERE `id`='$multi'")->fetchColumn();
						}
						$val = implode(', ',$arr_multi);
					}
				}
				
				//Если поле с датой, то форматируем в нужный формат 
				if($ftype[1]=='data'){
					$val = Helper::ChangeDateFormat($val, 'd.m.Y');
				}
				
				//если файловое поле, выводим ссылки на файлы
				if($ftype[1]=='file'){
                    $this->files = new files($this->table_name); 
					$val = implode('<br>',$this->files->FileListUrl($row['id']));
				}
					
				//если поле логическое (logical)
                if($ftype[1]=='logical'){
                    $val = $val==1 ? 'ДА' : 'НЕТ';
                }
                
                //если поле link выводим ссылку на id 
				if(isset($ftype[4]) and $ftype[4]=='link') 
					$val = '<a href="/'.$this->table_name.'/data/'.$row['id'].'">'.$val.'</a>';
					
				
				//если поле hidden - не выводим его
				$hidden = (isset($ftype[4]) and $ftype[4] == 'hidden') ? 1 : 0;
				
                if($hidden == 0) 
					$echo .= '<td><div>'.$val.'</div></td>';				
			}
			
			//ссылки на изменение/удаление записи
			$changelink = ($this->table_name=='user' && $row['id']==1) ? 0 : 1;
				$echo .= '<td class="edit_del_links">';
                if($changelink){
                    $echo .= $this->ChangeLink($row['id']);
                    $echo .= $this->DeleteLink($row['id']);
                }
				$echo .='</td>';
			$echo .= '</tr>';
		}
		$echo .= '</table>';
		
		return $echo;
	}
}