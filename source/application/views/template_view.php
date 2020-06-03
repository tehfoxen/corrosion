<?
$page = route::controller_name();
$array_menu = array(
            ''=>'Поиск',
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
			
if (isset($array_menu[$page])){
	$title = $array_menu[$page];
}
elseif(in_array($page,route::urlStruct())){
	$title = 'Структура. '.$array_menu['Структура'][$page];
}
else
	$title = '404';


?>
<!DOCTYPE HTML>
<html lang="ru">
  <head>
    <script src="/js/jquery-3.4.1.min.js"></script>
    <script src="/js/MultiSelect/jquery.multiselect.js"></script>    
    <script src="/js/jquery.tablesorter.min.js"></script> 
	<script src="/js/jquery.fancybox.min.js"></script>
	<script src="/js/jquery-ui/jquery-ui.min.js"></script>
    <script src="/js/jquery-ui/datepicker-ru.js"></script> 
	<script src="/js/jquery.maskedinput.min.js"></script> 
	<script src="/js/chosen/chosen.jquery.min.js"></script>
      <script src="/js/stacktable/stacktable.js"></script>
	
    <link rel="stylesheet" href="/js/jquery.fancybox.min.css" >       	
    <link rel="stylesheet" href="/js/table_sorter.css" />  
	<link rel="stylesheet" href="/js/chosen/chosen.min.css" />	
    <link rel="stylesheet" href="/js/stacktable/stacktable.css" />
      <link rel="stylesheet" href="/js/MultiSelect/jquery.multiselect.css" />
	<link rel="stylesheet" href="/js/jquery-ui/jquery-ui.css" />
    <link rel="stylesheet" href="/css/style.min.css" />
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    
    <script src="/js/script.js"></script>
    <meta charset="utf-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
  </head>
  <body>  
  <div class="main-container">


<header class="header">
<div class="header-middle">
<div class="container">
<div class="header-row">
<div class="header-row-first">
<div class ="header-row-first__logo">
<img alt="Logo" data-entity-type="" data-entity-uuid="" src="/images/logo.png">
</div>
<div class ="header-row-first__slogan">
<h2>БАЗА ДАННЫХ "КОРРОЗИЯ"</h2>
</div>
</div>
<div class="header-row-second">
<div class="header-row-second__location">
<div class="adress"><h4><img alt="Location" data-entity-type="" data-entity-uuid="" src="/images/location-ic.png"></h4>
<h4 class="adress__block">
Россия, Москва, ул. Радио, д.17<br>
Email:&nbsp;<a href="mailto:sale@isp.viam.ru">sale@isp.viam.ru</a><br>
Тел.:   +7 (499) 263-88-44<br>
       +7 (499) 263-86-48<br>
Факс: +7 (499) 267-86-09<br>
</h4></div>
</div>
</div>
</div>
</div>

</div>
   
   <nav class="navigation">
       <div class="navbar-header">
           <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
           </button>
       </div>
   <div class="container">
     <ul id="navigation-menu">

<?
            foreach ($array_menu as $k=>$v){               
				if(is_array($v)){				
				   $structure = 0;
                    foreach ($v as $key=>$val){
                        if(in_array($key, Route::getPageRole()))
							$structure = 1;
                    }
                    if($structure){                   
                        $active = (in_array($page,route::urlStruct()) and $page!='user') ? ' class=active ' : '';
                        echo '<li'. $active.'><a>'.$k.'</a>
						
<i class="ddl-switch fa fa-angle-down"></i>
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
            }?>
        </ul>
      </nav>
	  </header>
  <div class="container-fluid no-padding page-content">

      <div class="container">
                  <div class="region-content">
                            <?php include 'application/views/'.$content_view; ?>
                            </div>
      </div>
  </div>
      <footer class="footer-main">
	  <div class="container">
          <div class="footer-content">
              <div class="footer-content__row">
	  <div class="contact-details">
	  <div class="address-box detail-box">
          <p><i><img alt="Loactaion" data-entity-type="" data-entity-uuid="" src="/images/ftr-location.png"></i></p><p>Россия, Москва<br>ул. Радио, д.17</p>
      </div>
	  <div class="phone-box detail-box">
          <p><i><img alt="Phone" data-entity-type="" data-entity-uuid="" src="/images/ftr-phone.png"></i></p><p>Тел.: +7 (499) 263-88-44<br>&nbsp; &nbsp; &nbsp; &nbsp; +7 (499) 263-86-48</p>

      </div>
	  <div class="mail-box detail-box">
          <p><i><img alt="Email" data-entity-type="" data-entity-uuid="" src="/images/ftr-email.png"></i></p><p>Email: sale@isp.viam.ru<br>Факс: +7 (499) 267-86-09</p>
      </div>
	  </div>
              </div>
          </div>
	  <div class="footer-info">
	  <div class="footer-info__region-first"></div>
	  <div class="footer-info__region-second"></div>
	  </div>
	  </div>
      <footer>
      <!-- Подвал сайта-->
      </footer>
  </body>
</html>

