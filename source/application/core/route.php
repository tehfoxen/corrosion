<?
class Route
{
    static function host(){
        return $_SERVER['HTTP_HOST'];
    }
    
    static function hostlink(){
        return 'http://' . self::host() . '/';
    }
    
    static function urlPars(){
        $explode = explode('?', $_SERVER['REQUEST_URI']);
		//return explode('/', $_SERVER['REQUEST_URI']);
		return explode('/', $explode[0]);
    }
    
    static function controller_name(){
        return self::urlPars()[1];
    }
    
    static function action_name(){
        if(isset(self::urlPars()[2]))
            return self::urlPars()[2];
    }
    
    static function arg(){
        if(isset(self::urlPars()[3]))
            return self::urlPars()[3];
    }
    
    static function urlStruct(){
        //массив адресов, которые будут обрабатываться одним и тем же контроллером, моделью и вьюхой
        return array('user', 'category', 'gost', 'method', 'unit', 'parametr', 'discrete');
    }
    
    //видимость разделов
	static function getPageRole(){
        if(!ROLE)
            return ['','standart','login','data','quicksearch','search','stat'];
        elseif(ROLE==2)
            return ['','input','standart','data','mydata','logout','quicksearch','search','stat'];
        elseif(ROLE==1)
            return ['','input','standart','data','mydata','user','userdata','category','gost','method','unit','parametr','discrete','logout','quicksearch','search','stat'];
    }
    
    static function start(){
        // контроллер и действие по умолчанию
		$controller_name = 'main';
		$action_name = 'index';
		
		// получаем имя контроллера
		if ( !empty(self::controller_name()) )
			$controller_name = self::controller_name();
        
        if(in_array(self::controller_name(),self::urlStruct())){
            $controller_name = 'structure';
            if ( empty(self::action_name()) ){
                header('Location:'.self::hostlink().self::controller_name().'/add');
            }
        }
        
		// получаем имя экшена
		if ( !empty(self::action_name()) )
			$action_name = self::action_name();
        
        //Аргументы
			$arg = !empty(self::arg()) ? self::arg() : '';

		// добавляем префиксы
		$model_name = 'Model_'.$controller_name;
		$controller_name = 'Controller_'.$controller_name;
		$action_name = 'action_'.$action_name;

		// подцепляем файл с классом модели (файла модели может и не быть)
		$model_file = strtolower($model_name).'.php';
		$model_path = "application/models/".$model_file;
		if(file_exists($model_path))
			include "application/models/".$model_file;

		// подцепляем файл с классом контроллера
		$controller_file = strtolower($controller_name).'.php';
		$controller_path = "application/controllers/".$controller_file;
        
        //если страница вне доступа для роли пользователя
        if(!in_array(self::controller_name(), self::getPageRole()))
            self::ErrorPage404();
        
        if(file_exists($controller_path))
            include "application/controllers/".$controller_file;
		else
            self::ErrorPage404();
		
		// создаем контроллер
		$controller = new $controller_name;
		$action = $action_name;
		
		if(method_exists($controller, $action)){
			// вызываем действие контроллера
			$controller->$action($arg);
		}
		else{
			// здесь также разумнее было бы кинуть исключение
			self::ErrorPage404();
		}
	}
	
	static function ErrorPage404(){
        header("HTTP/1.0 404 Not Found");
        header("HTTP/1.1 404 Not Found");
        //die('404 not found');
        $obj = new view();
        $obj->generate('view_404.php', 'view_template.php');
        die();
    }
}