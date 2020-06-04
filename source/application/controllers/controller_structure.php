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
	function showEllem($data){
		$obj = new showform();	
		$data['title'] = $obj->ShowTblName();
		$data[] = $obj->getForm();
		
		$obj = new showlist();
		$data[] = $obj->getList();
		
		$this->view->generate('view_structure.php', 'view_template.php', $data);
	}
	
    function action_edit(){       
        $this->action_change();		
    }
    
    function action_add(){        
        $this->action_change();	  
	}
	
	function action_change(){        
        if((isset($_POST['add']) or isset($_POST['edit'])) && $this->change) {
			try { 
				$this->model->change_data(); 					
				$this->headerToStart();
			}
			catch (Exception $e) {
				if(strpos($e->getMessage(),'Duplicate entry'))
					$data['error'] = 'Такая запись уже есть в базе данных';
				else 
					$data['error'] = 'Что-то пошло не так...';
				//$e->getMessage();
				//'Что-то пошло не так...';
			}
        }
		$this->showEllem($data);
	}
	
    function action_del(){        
        if($this->change){
           try{
			   $this->model->del_data(); 
			   $this->headerToStart();			   
		   }
		   catch (Exception $e){
			    if(strpos($e->getMessage(),'Integrity constraint violation'))
				   $data['error'] = 'Нельзя удалить запись, поскольку с ней связаны другие записи в Базе данных';
				else 
					$data['error'] = 'Что-то пошло не так...';
				$this->showEllem($data);
		   }
		   
        }
	}
	
	//Вывод данных, добавленных пользоваетлем. Метод актуален только для раздела /user
	function action_data(){
	   $user_id = route::arg();
	   include('application/models/model_mydata.php');
	   $obj = new Model_Mydata();
	   $data['title_page'] = Helper::UserName($user_id);
	   $data[] = $obj->GetMyData($user_id);	
	   $this->view->generate('view_mydata.php', 'view_template.php', $data);
    } 
    
    function headerToStart(){
       header('Location:'.Route::hostlink().Route::controller_name().'/add');
    }
    
    
}