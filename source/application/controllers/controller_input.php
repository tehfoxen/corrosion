<?
class Controller_Input extends Controller {
    
    function __construct(){
		parent::__construct();
        $this->model = new Model_Input(); 
	}
    
    function action_index(){
       $this->view->generate('view_input.php', 'view_template.php');
    }
    
    function action_start(){      
        if(route::arg() == 1){
            $all_gost = helper::GetGost();	
            include 'application/views/view_input_ajax.php';
        }
        if(route::arg() == 2){
            $all_param = $this->model->GetAllParam();	
            include 'application/views/view_input_ajax.php';
        }
		if(route::arg() == 3){
            $early_added = $this->model->GetEarlyAdded();	
            include 'application/views/view_input_ajax.php';
        }
    }
    
    function action_gostlist(){      
        //$all_method = $this->model->GetMethod();
		$all_method = helper::GetMethod(route::arg());
        include 'application/views/view_input_ajax.php';
    }
    
    function action_methodlist(){      
        $param_array = $this->model->GetForm();
        include 'application/views/view_input_ajax.php';
    }
    
    function action_paramlist(){ 
		//var_dump($_POST['choose_param']);
        if($_POST['choose_param']){
			$paramlist = implode(',',$_POST['choose_param']);
		
			//$paramlist = implode(',',$_POST['paramlist']);
			$param_array = $this->model->GetForm($paramlist);
			include 'application/views/view_input_ajax.php';
		}
    }
	
	function action_earlyadded(){
		$param_array = $this->model->GetParamList(route::arg());
        include 'application/views/view_input_ajax.php';
	}
    
	function action_add(){    
		try{
			$this->model->DataAdd();
			if(!route::arg()){
				$data = 'Данные успешно добавлены, их можно увидеть в разделе <a href="/mydata">Мои данные.</a><br><a href="/input">Добавить еще данные</a>';			
				$this->view->generate('view_input.php', 'view_template.php', $data);
			}
		}catch (Exception $e) {
			echo $e->getMessage();					
		}
    }
}