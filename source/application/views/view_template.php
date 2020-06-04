<?
$page = route::controller_name();
$array_menu = array(
            'Поиск'=>array(
				''=>'Расширенный поиск по параметрам', 
				'quicksearch'=>'Быстрый поиск'),
            'input'=>'Ввод данных',
            'mydata'=>'Мои данные',
            'standart'=>'ГОСТ',
            'Структура'=>array(
                'category'=>'Категории',
                'gost'=>'Госты',
                'method'=>'Методы испытаний',
                'unit'=>'Единицы измерений',
                'parametr'=>'Параметры',
                'discrete'=>'Дискретные типы'),
            'user'=>'Пользователи',
            'login'=>'Вход',
            'logout'=>'Выход ' . NAME);

if(in_array($page,route::getPageRole())){	
			
	$title = helper::SearchMultiArray($array_menu, $page);
	if(!$title)
		$title = '404';
	$title_heder = $title;
	if(in_array($page,route::urlStruct()))
		$title_heder = 'Структура. '.$title;
	if($page == 'user' and route::action_name() == 'data' and route::arg())
		$title = helper::UserName(route::arg());
}

?>
<!DOCTYPE HTML>
<html lang="ru">
  <head>
    <script src="/js/jquery-3.4.1.min.js"></script>     
	<script src="/js/tablesorter/jquery.tablesorter2.min.js"></script> 
	<script src="/js/jquery.floatThead.min.js"></script> 	
	<script src="/js/jquery.fancybox.min.js"></script>
	<script src="/js/jquery-ui/jquery-ui.min.js"></script>
    <script src="/js/datepicker-ru.js"></script> 
	<script src="/js/jquery.maskedinput.min.js"></script> 
	<script src="/js/chosen/chosen.jquery.min.js"></script>
	<script src="https://polyfill.io/v3/polyfill.min.js?features=es7"></script>  <!--для работоспособности некоторых js-функций в IE ->
	
	<!--плагин мультиселект (чекбоксы). ПЕРЕИМЕНОВЫВАЕМ его, поскольку название совпадает с другим плагином	-->
	<script src="/js/MultiSelect/jquery.multiselect.js"></script>  
	<script>
			$.fn.multiselect_check = $.fn.multiselect;
	</script>
	<link rel="stylesheet" href="/js/MultiSelect/jquery.multiselect.css" />
	
	<!--плагин мультиселект (выбор из одного селекта в другой)-->
	<script src="/js/lou-multi-select/js/jquery.multi-select.js"></script> 
	<script src="/js/jquery.quicksearch.js"></script>		
	<link rel="stylesheet" href="/js/lou-multi-select/css/multi-select.css" />
		
    <link rel="stylesheet" href="/js/jquery.fancybox.min.css" >       	
	<link rel="stylesheet" href="/js/tablesorter/css/theme.default.css" /> 
	<link rel="stylesheet" href="/js/chosen/chosen.min.css" />	
    <link rel="stylesheet" href="/js/MultiSelect/jquery.multiselect.css" />
	<link rel="stylesheet" href="/js/jquery-ui/jquery-ui.css" />
	<link rel="stylesheet" href="/css/style.css" />
    
    <script src="/js/script.js"></script>    
	<meta charset="utf-8"/>    
	<title><?=$title_heder?></title>
	
  </head>
  <body>  
      <header>
      <!-- Шапка сайта-->
      </header>
      <nav>
        <ul id="menu"><?
			foreach ($array_menu as $k=>$v){               
				if(is_array($v)){				
				   $structure = 0;
                    foreach ($v as $key=>$val){
                        if(in_array($key, Route::getPageRole()))
							$structure = 1;
                    }
                    if($structure){                   
						$active = $v[$page] ? ' class=active ' : '';						
						echo '<li'. $active.'><a>'.$k.'</a>
                               <ul id="submenu">';
                               foreach($v as $key=>$val){
                                   $active = $key == $page ? ' class=active ' : '';
								   echo '<li'. $active.'><a href="/'.$key.'">'.$val.'</a></li>';
                               }
                        echo '</ul>';
                    }
                }
                else{               
					if(in_array($k, Route::getPageRole())){                    
                        $active = $k == $page ? ' class=active ' : '';
						echo '<li'. $active.'><a href="/'.$k.'">'.$v.'</a></li>';
                    }
                }
            }
			
			?>
        </ul>
      </nav>
	  <h1 class="spr_title"><?=$title?></h1>
      <? include 'application/views/'.$content_view; ?>
      <footer>
      <!-- Подвал сайта-->
      </footer>
  </body>
</html>

