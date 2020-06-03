<? class Files {
        public $upload_path;
        
        function __construct($table_name = null){
			$this->upload_path = 'upload/' . $table_name;	           
        }

	/** Удаление файла по имени файла без расширения */
	function FileDelete($name){
        //error_reporting(E_ERROR);
        try {
            $filename = glob($this->upload_path.'/'.$name.'.*'); 
            foreach ($filename as $val)
				if(file_exists($val))	
					unlink($val);
        }
        catch (Error $e) {
            echo "Произошла ошибка: " . $e->getMessage() . "\n";
        }
	}
	
	/** Список файлов */
	function FileListUrl($id){
		$arr = array();
		$filename = glob($this->upload_path.'/'.$id.'.*');
		if(count($filename)){
			foreach($filename as $val){
				$explode = explode('/',$val);
				$filename = end($explode);
				$arr[$filename] = '<a href="/'.$val.'" target="_blank">'.$filename.'</a>';
			}
		}
		return $arr;
	}
    
    /** ссылка на файл */
	function FileHref($id){		
		$filename = glob($this->upload_path.'/'.$id.'.*')[0];
        $explode = explode('/',$filename);		
		//return '<a href="/'.$filename.'" target="_blank">'.end($explode).'</a>';
        return $filename;        
	}
    
    /** получить расширение */
	function GetExt($id){		
		$filename = glob($this->upload_path.'/'.$id.'.*')[0];
        $arr = explode('.',$filename);
        return end($arr);	       
	}
    
	
	/** Проверка на ошибки массива $_FILES */
	function FilesNotAmpty(){
		if($_FILES){
			$error = $_FILES['file']['error'];
			$er = (count($error)>1) ? max($error) : $error[0];				
			if ($er==0) return 1;
			else {
				$this->errors[] = $er;
				return false;
			}
		}
		else 
            return false;
	}

  /** Создание каталога */
	function MakeDir(){
		//echo 'MakeDir - $this->upload_path = '.$this->upload_path.'<br>';
        
        if(file_exists($this->upload_path)== false) {
            
			if (!mkdir($this->upload_path)) {
               echo 'Не удалось создать директорию';
				//$this->errors['mkdir']='Не удалось создать директорию';
				//throw new Exception('Не удалось создать директорию для загрузки файлов');
			}
		}		
	}    
 
/** Загрузка файла 
	$id - имя будущего файла без расширения (необязательно именно id - любая строка может быть), 
	$filename - имя файлового поля в форме добавления файла */   
    function FileUpload($id,$filename='file'){		
		$error_upload = array();				
		$this->MakeDir();

		//узнаем расширение файла
        $ext = pathinfo($_FILES[$filename]['name'], PATHINFO_EXTENSION); 
				
		//новое имя
        $file_name = $this->upload_path.'/'.$id.'.'.$ext;
        
        //загружаем файл		
        if(!move_uploaded_file($_FILES[$filename]['tmp_name'],$file_name)) {
             echo 'Ошибка загрузки файла ('. __METHOD__ .': '.__LINE__.') ';	
        }
	}
	
	//поиск файлов определенной сущности. Возвращает массив id параметров, к которым принадлежат файлы сущности. В качестве ключа - путь к файлу.
	public function FileEntity($entity_id){
		$arr = array();
		$filename = glob($this->upload_path.'/'.$entity_id.'_*');
		if(count($filename)){
			foreach($filename as $val){
				$explode = explode('/',$val);				
				$explode = explode('.',end($explode))[0];
				$arr[$val] = explode('_',$explode)[1];
			}
		}
		return $arr;
	}
}

