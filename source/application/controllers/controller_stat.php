<?
class Controller_Stat extends Controller {
	function __construct(){
		parent::__construct();
        $this->model = new Model_Stat(); 
	}
	
	function action_statprepare(){		
		//var_dump($_POST['paramid_array']);
		echo $this->model->GetStatSelect();
	}
	
	function action_statresult(){
		//var_dump($_POST['stat_array']);
		$data =  $this->model->GetStatResult();
		//var_dump($data);
		echo json_encode($data);		
	}
}