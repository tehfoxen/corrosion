<?
class Controller_Structure extends Controller
{
    function __construct(){
		$this->model = new Model_Structure();
		$this->view = new View();
			
        //запрет на правку учетки Admin в разделе user
        $this->change = (route::controller_name() == 'user' and route::arg()==1) ? 0 : 1;
	}
    
	//отображение формы добавления/редактирования и таблицы с уже добавленными данными
	function showEllem(){
		$obj = new showform();	
		$data['title'] = $obj->ShowTblName();
		$data[] = $obj->getForm();
		
		$obj = new showlist();
		$data[] = $obj->getList();
		
		$this->view->generate('structure_view.php', 'template_view.php', $data);
	}
	
    function action_edit(){       
        if(isset($_POST['edit']) && $this->change) {
           $this->model->change_data();
           $this->headerToStart();
        } 
		$this->showEllem();
    }
    
    function action_add(){        
        if(isset($_POST['add']) && $this->change) {
           $this->model->change_data();  
           $this->headerToStart();
        }
		$this->showEllem();
	}
    
    function action_del(){        
        if($this->change){
           $this->model->del_data(); 
           $this->headerToStart();
        }
	}
	
	//Вывод данных, добавленных пользоваетлем. Метод актуален только для раздела /user
	function action_data(){
	   $user_id = route::arg();
	   include('application/models/model_mydata.php');
	   $obj = new Model_Mydata();
	   $data['title_page'] = Helper::UserName($user_id);
	   $data[] = $obj->GetMyData($user_id);	
	   $this->view->generate('mydata_view.php', 'template_view.php', $data);
    } 
    
    function headerToStart(){
        header('Location:'.Route::hostlink().Route::controller_name().'/add');
    }
    
    
}