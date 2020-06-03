<?
class Controller {
	
	public $model;
	public $view;
	
	function __construct()
	{
		$this->view = new View();
	}
	
	function action_index()
	{
	}
	
	function headerToStart(){
        header('Location:'.Route::hostlink().Route::controller_name());
    }
}