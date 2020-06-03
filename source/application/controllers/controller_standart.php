<?
class Controller_Standart extends Controller {
    
    function action_index(){
       $this->model = new Model_Standart(); 
       $data = $this->model->get_data();		
       $this->view->generate('standart_view.php', 'template_view.php', $data);
    }
}