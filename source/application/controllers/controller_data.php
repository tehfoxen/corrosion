<?
class Controller_Data extends Controller {
    
  function __construct(){
		parent::__construct();
        $this->model = new Model_Data(); 
	}
	
	function action_getfield(){       
	   //$obj= new Model_Data();
	   $field = $this->model->GetField();	   
	   include 'application/views/data_ajax_view.php';
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
		$role = $_SESSION['user_role'];
		$userid_add_entity = helper::UserDataAdd(route::arg());
		$userid = $_SESSION['user_id'];
		$data['access'] = ($role == 1 or $userid == $userid_add_entity) ? 1 : 0;	
		
		/* echo '--role='.$role;	
		echo '--userid='.$userid;	
		echo '--userid_add_entity='.$userid_add_entity;	 */
	   
	   include 'application/views/mydata_ajax_view.php';
    } 
    
}