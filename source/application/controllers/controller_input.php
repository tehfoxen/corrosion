<?
class Controller_Input extends Controller {
    
    function __construct(){
		parent::__construct();
        $this->model = new Model_Input(); 
	}
    
    function action_index(){
       $this->view->generate('input_view.php', 'template_view.php');
    }
    
    function action_start(){      
        if(route::arg() == 1){
            $all_gost = $this->model->GetGost();	
            include 'application/views/input_ajax_view.php';
        }
        if(route::arg() == 2){
            $all_param = $this->model->GetAllParam();	
            include 'application/views/input_ajax_view.php';
        }
		if(route::arg() == 3){
            $early_added = $this->model->GetEarlyAdded();	
            include 'application/views/input_ajax_view.php';
        }
    }
    
    function action_gostlist(){      
        $all_method = $this->model->GetMethod();
        include 'application/views/input_ajax_view.php';
    }
    
    function action_methodlist(){      
        $param_array = $this->model->GetForm();
        include 'application/views/input_ajax_view.php';
    }
    
    function action_paramlist(){ 
        $paramlist = implode(',',$_POST['paramlist']);
        $param_array = $this->model->GetForm($paramlist);
        include 'application/views/input_ajax_view.php';
    }
	
	function action_earlyadded(){
		$param_array = $this->model->GetParamList(route::arg());
        include 'application/views/input_ajax_view.php';
	}
    
    function action_add(){      
       // print_r ('<pre>');
		//var_dump($_POST);
		//var_dump($_FILES);
		//print_r ('</pre>');
		$this->model->DataAdd();
		
		$data = 'Данные успешно добавлены, их можно увидеть в разделе <a href="/mydata">Мои данные.</a><br><a href="/input">Добавить еще данные</a>';
		$this->view->generate('input_view.php', 'template_view.php', $data);
    }
    
 
        
    
}