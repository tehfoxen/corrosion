<?
class Structure{	
	
	public $table_name;	
    public $action_name;
	public $fields;	
    public $arg;
		
    function __construct(){
		include('application/db_tables.php');
        
        $this->table_name = route::controller_name();
        $this->action_name = route::action_name();
        $this->arg = route::arg(); 
        $this->fields = $tbl_array[$this->table_name];                     
	}

    /** Название таблицы  */
	function ShowTblName(){
		return explode('@',$this->fields[0])[0];
	}
}