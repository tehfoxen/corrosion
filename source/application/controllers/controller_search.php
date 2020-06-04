<?
class Controller_Search extends Controller {
    
    function __construct(){
		parent::__construct();
        $this->model = new Model_Search(); 
	}
	
	function action_chooseparam(){
		//var_dump($_POST);
		if($_POST){
			$data['choose_param'] = $this->model->GetSelectQuery();
			//var_dump($data);
			//$this->view->generate('view_main.php', 'view_template.php',$data);
			include 'application/views/view_search_ajax.php';
		}
	}

	function action_selectquery(){
		//var_dump($_POST);
		if($_POST['param_id']){
			$paramid = $_POST['param_id'];
			$data = $this->model->GetQueryField($paramid);
			echo $data;
		}
	}
	
	function action_search(){
		//var_dump($_POST);
		if($_POST){			
			$data['search_result'] = $this->model->SearchResultParam();
			include 'application/views/view_search_ajax.php';
		}
	}
	
	
}