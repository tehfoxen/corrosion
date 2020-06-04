<?
class Controller_Quicksearch extends Controller
{
	function action_index()
	{	
		//var_dump($_POST);
		
		$obj = new Model_Quicksearch();
		
		if($_REQUEST['search'])			
			$data['quick_search'] = $obj->SearchResult();
		
		$this->view->generate('view_quicksearch.php', 'view_template.php',$data);
	}
	
	
	
	
}