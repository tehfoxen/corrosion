<?
class Controller_Mydata extends Controller {
    
    function __construct(){
		parent::__construct();
        $this->model = new Model_Mydata(); 
	}
    
    function action_index(){
	   $data[0] = $this->model->GetMyData();
	   //var_dump($data[0])	;  
	   $this->view->generate('view_mydata.php', 'view_template.php', $data);
    } 
	
	
}