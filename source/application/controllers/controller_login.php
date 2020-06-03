<?php

class Controller_Login extends Controller
{
	
	function action_index()	{		
        if(isset($_POST['email']) && isset($_POST['password'])){
            $obj = new Model_Login();
			$data['error'] = $obj->GetUser(); 			
        }
		$data['loginform'] = $this->ShowLoginForm();	
		
		$this->view->generate('login_view.php', 'template_view.php', $data);
	}
	
	function action_confirm()	{		
		$obj = new User();
		$data['confirm'] = $obj->UserRegFinally();		
		$this->view->generate('login_view.php', 'template_view.php', $data); 
	}
	
	function ShowLoginForm(){		
		$obj = new showform();			
		return $obj->getForm();
	}
	
}