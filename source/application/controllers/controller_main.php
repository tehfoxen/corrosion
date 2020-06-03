<?
class Controller_Main extends Controller
{
	function action_index()
	{	
		if($_REQUEST['search']){
			$obj = new Model_Main();
			$data = $obj->SearchResult();
		}
			
		$this->view->generate('main_view.php', 'template_view.php',$data);
	}
	
	
}