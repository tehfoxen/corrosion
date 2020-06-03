<?
class Controller_Mydata extends Controller {
    
    function __construct(){
		parent::__construct();
        $this->model = new Model_Mydata(); 
	}
    
    function action_index(){
	   $data[0] = $this->model->GetMyData();
	   //var_dump($data[0])	;  
	   $this->view->generate('mydata_view.php', 'template_view.php', $data);
    } 
	
	function action_entitydel(){
		$this->model->DelEntity();	
		header('location:'.$_SERVER['HTTP_REFERER']);
		//$this->headerToStart();
	}
}