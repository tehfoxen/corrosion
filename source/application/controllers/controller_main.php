<?
class Controller_Main extends Controller
{
	function __construct(){
		parent::__construct();
        $this->model = new Model_Main(); 
	}
	
	function action_index()	{	
		$data['param_search'] = $this->model->GetParam();			
		$this->view->generate('view_main.php', 'view_template.php',$data);
	}
	
	
	
}