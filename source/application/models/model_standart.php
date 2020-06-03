<?
class Model_Standart extends Model
{
	public $files;
    public function get_data()
	{        
        $this->files = new files('gost'); 
        $array_ext = array('pdf'=>'images/pdf.png', 'doc'=>'images/word.png', 'docx'=>'images/word.png');
        //GetExt($id)
        $stmt = DB::run("SELECT `name`, id FROM `gost` ORDER BY `name` ASC")->fetchAll(PDO::FETCH_KEY_PAIR);
        
          /*var_dump($stmt);
          
              '9.021' => string 'upload/gost/37.pdf' (length=18)
              '9.401' => string 'upload/gost/40.pdf' (length=18)
              '9.904' => string 'upload/gost/38.pdf' (length=18)
              '9.909' => string 'upload/gost/39.pdf' (length=18)*/
          
          
        foreach($stmt as $k=>$v){
            $link = $this->files->FileHref($v);
            $ext = $this->files->GetExt($v);
            $data[$k] = array($link,$array_ext[$ext]);
        }  
        //array_walk($stmt, function(&$value, $key){$value = $this->files->FileHref($value);});
       
        //return $stmt;
        return $data;
	}
}