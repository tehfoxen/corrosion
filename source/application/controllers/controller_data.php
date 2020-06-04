<?
class Controller_Data extends Controller {
    
  function __construct(){
		parent::__construct();
        $this->model = new Model_Data(); 
	}
	
	function action_getfield(){       
	   //$obj= new Model_Data();
	   $field = $this->model->GetField();	   
	   include 'application/views/view_data_ajax.php';
    }
	
	function action_delete(){       
	   //$obj= new Model_Data();
	   $this->model->DeleteData(); 
    }
	
	function action_edit(){       
	   //$obj= new Model_Data();
	   $this->model->EditData(); 
    }
	
	function action_entity(){       
	    $data[0] = $this->model->GetEntity();	
		//var_dump($data[0]);
		//die;
		$role = $_SESSION['user_role'];
		$userid_add_entity = helper::UserDataAdd(route::arg());
		$userid = $_SESSION['user_id'];
		$data['access'] = ($role == 1 or $userid == $userid_add_entity) ? 1 : 0;	
		
		/* 
		echo '--role='.$role;	
		echo '--userid='.$userid;	
		echo '--userid_add_entity='.$userid_add_entity;	 
		*/
	   
	   include 'application/views/view_mydata_ajax.php';
    } 
	
	function action_entitydel(){
		$this->model->DelEntity();	
		header('location:'.$_SERVER['HTTP_REFERER']);
		//$this->headerToStart();
	}
    
}