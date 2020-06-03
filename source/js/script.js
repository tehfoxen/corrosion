$(document).ready(function() {

/** ПЛАГИН селект мультиплай в чекбокс*/
$('select[multiple]').multiselect({
    columns  : 1,
    search   : false, 
    maxWidth :700,
	selectAll: true,
    texts    : {
        placeholder: 'Выберите методы',
    }
});    
  
/** ПЛАГИН Сортировка таблиц.  tablesorter.min.js */
$("#parametr, #method, #discrete, #user, #mydata, #early, #searchresult").tablesorter(); 

/** ПЛАГИН Календарь jquery UI Datepicker */	
function datepicker(){
	$('.idate').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-62:+2"
	}); 
	 /** ПЛАГИН маска на поле  */
	 $(".idate").mask("99.99.9999");
}

datepicker();

/** ПЛАГИН Chosen - поле поиска в селекте. Подключаем для селектов, в которых список больше 10*/
function chosen(){
	$('select').each(function(indx, element){	
		if($(element).find('option').length>=10 && !$(element).attr('multiple') ){
			$width = $(element).width()+11;
			$(element).chosen({no_results_text: "Не найдено", placeholder_text_single: " ", width: $width});
						
			if($(element).attr('required') == 'required'){
				$(element).next('.chosen-container-single').attr('required','required');
			}
		}
	});
}

chosen();

var $host = window.location.hostname;

/** Стиль для обязательных полей */	
$("input,select,textarea,div").each(function(){	
	if($(this).attr('required'))
        $(this).addClass('required');
});

/** ВХОД. Убрать легенду из формы авторизации */
$('fieldset.login legend').remove();

/** СТРУКТУРА. Копирование значения из символьного поля в скрытое поле инпут в формах с псевдотипом "symbol" */
	$('#spr_form').on('keyup', '.symbol_field',  function(){
		var $this=$(this);
		var value = $this.html();
		$(this).next('input').val(value); 		
	}); 
	
/** СТРУКТУРА. Удаление записи из таблиц в разделе  */
	$('.spr_table').on('click', '.del_record',  function(){
		return confirm('Удалить запись?');
	});

/** СТРУКТУРА Скрываем поле "Множественный" */
	var $multiselect_field = $('#spr_form.parametr').find('[name=multiselect]').parent('div')
	$multiselect_field.hide();

/** СТРУКТУРА. Показываем поле "Множественный" при выборе типа данных "дискретный" */
$('#spr_form.parametr').on('change', 'select[name=datatype_id]', function(){
	if($(this).val() == 8)
		$multiselect_field.show();
	else
		$multiselect_field.hide();
});

/** СТРУКТУРА. Если поле "Шаблон"=ДА, то поле "Обязательный"=ДА */
$('#spr_form.parametr').on('change', 'select[name=pattern]', function(){
	if($(this).val() == 1)
		$('select[name=required]').val(1);		
});

/** ВВОД ДАННЫХ. выбор из селекта start на странице */
$('body').on('change', 'select.start', function(){
    var $this = $(this);    
    if($this.val()){
        //$('.block1, .block2').html('');
        $("[class *= iblock]").html('');
        $.post('https://' + $host + '/input/start/' + $this.val(), {}, function(response){
			if(response){
				$('.iblock1').html(response);
				$("#early").tablesorter(); 
			}
        });
    }
});

/** ВВОД ДАННЫХ. выбор из селекта gostlist на странице */
$('body').on('change', 'select.gostlist', function(){
    var $this = $(this);    
    if($this.val()){
        $.post('https://' + $host + '/input/gostlist/' + $this.val(), {}, function(response){
			if(response){
				$('.iblock2').html(response);
				$('.iblock3').html('');
			}
        });
    }
});

/** ВВОД ДАННЫХ. выбор из селекта methodlist на странице */
$('body').on('change', 'select.methodlist', function(){
    var $this = $(this);  
    $('.iblock3').html('');
    if($this.val()){
        $.post('https://' + $host + '/input/methodlist/' + $this.val(), {}, function(response){
			if(response){				
				$('.iblock3').html(response);				    
				datepicker();
				chosen();
				$('[name = method_id]').val($this.val());
				
				/* удаление поля "Описание испытания" param_165 из формы, которая формируется при выборе "испытания в соответствии с ГОСТ" */
				$('[name = param_165]').remove();
			}
        });
    }
});

/** ВВОД ДАННЫХ. Отправка выбранных параметров на странице */
$(".iblock1").on("submit", "form.paramlist",function(e){    
 		e.preventDefault(); //отмена события по умолчанию. в данном случае сабмита формы нч
		$('.iblock1 form.paramlist').hide();
			/* Отправляем поля формы в submit.php: */
			$.post('https://' + $host + '/input/paramlist',$(this).serialize(),function(response){
				if(response)
                    $('.iblock3').html(response);
					datepicker();
					chosen();
			});
});

/** ВВОД ДАННЫХ. Отправка ранее введенного испытания. Отправляется id сущности. Получаем форму на основе параметров этой сущности  */
$(".iblock1").on("click", "#early td",function(){    		
		$('.iblock1 #early').hide();
			var $entity_id = $(this).attr('id');
			var $method_id = $(this).find('div').attr('data-method');
			var $testname = $(this).html();
			console.log($entity_id);
			$.post('https://' + $host + '/input/earlyadded/' + $entity_id, function(response){
				if(response)
                    $('.iblock3').html(response);
					datepicker();
					chosen();
					
					if($method_id){
						$('[name = method_id]').val($method_id);
						/* удаление поля "Описание испытания" param_165 из формы, которая формируется при выборе "испытания в соответствии с ГОСТ" */
						$('[name = param_165]').remove();
					}
					else
						$('[name = param_165]').val($testname);
					
			});
});


/** ВВОД ДАННЫХ. Отправка данных формы */
//по какой из двух кнопок сабмит был клик
var $press_submit = null; 
$(".iblock3").on('focus', ':submit', function(){	  
    $press_submit = $(this).attr('class');	
});

$(".iblock3").on('submit', "form", function(e){    
	//при нажатии кнопки "Отправить и внести еще образец"
	if($press_submit=='many'){
		e.preventDefault();		
		
		var form = $(this); 
		
		$sample_param = form.find('input[name=param_27]');
		$sample = $sample_param.val();
		
		$('[name=add_data]').hide();
		$('.loading').show();
		
		formData = new FormData(form.get(0));
		
		$.ajax({
		  url: 'https://' + $host + '/input/add',
		  type: 'POST',
		  data: formData,
		  contentType: false,
		  cache: false,
		  processData:false,
		  success: function(response) {
			$('.input_anonce').show().html('Информация об образце <b>' + $sample + '</b> внесена. <br>Актуализируйте форму и отправьте данные о следующем образце');
			$sample_param.val('');
			$('[name=add_data]').show();
			$('.loading').hide();
			$('body,html').animate({scrollTop: 0}, 100);
		  }
		});
    }
});

/** МОИ ДАННЫЕ. открытие карточки образца (сущности) во всплывающем окне */
$('#mydata, #searchresult').on('click', 'a.fancybox',  function(e){
	e.preventDefault();
    var $this = $(this);
	var $url = $this.attr('href');

	
	$.post('https://' + $host + $url, {}, function(respons){


		if(respons) 				
			$.fancybox.open(respons,{touch: false});

	});
 });

/** МОИ ДАННЫЕ. Удаление сущности (всей инфы об образце) */
	$('body').on('click', '.entity_del',  function(){
		return confirm('Удалить всю информацию о данном образце?');
	});

/** МОИ ДАННЫЕ. Клик по ссылке Режим редактирования */
	$('body').on('click', '.entity_edit',  function(){
		var $this = $(this);
		var $mod = $this.attr('id');
		var $title_edit = 'Режим редактирования';
		var $title_read = 'Режим чтения';
		
		if($mod == 'edit'){
			$this.html($title_read);
			$this.attr('id','read');
			$('.edit_value').show();
			$('.entity').css({'background-color': '#eff4f8'});
			$( ".entity" ).find( "fieldset" ).removeClass("background-read").addClass( "background-edit" );
            $( ".entity" ).find( "fieldset legend" ).css('color','green');			
		}
		else{
			$this.html($title_edit);
			$this.attr('id','edit');
			$('.edit_value').hide();
			$('.entity').css({'background-color': 'white'});
			$( ".entity" ).find( "fieldset" ).removeClass("background-edit" ).addClass( "background-read" );
            $( ".entity" ).find( "fieldset legend" ).css('color','#0641ab');
		}
	});


function start_condition(){
	$('.parametr').css({'opacity':'1'});
	$('.edit_value').show();
	$('.cancel, .del, .edit').remove();
}

/** МОИ ДАННЫЕ. Клик по символу редактирования (карандашу) в карточке образца*/
	$('body').on('click', '.edit_value',  function(){
		var $this=$(this);
		var $id = $this.parent('.parametr').attr('id'); //3_11_shorttext		
		var $arr = $id.split('_');
		var $param_id = $arr[1];
		var $datatype = $arr[2];
		var $entity = $arr[0];
		var $value = $this.siblings('.value').html();
		var $flag = 1;
		var $required;
		var $value_edit;
		var $value_select;

		$('.parametr').css({'opacity':'0.2'});
		$('#'+$id).css({'opacity':'1'});
		$('.edit_value').hide();
		
		$.post('https://' + $host + '/data/getfield/' + $param_id, {entity:$entity, data_type:$datatype, value:$value}, function(response){
			if(response){
				//вывод поля 
				$this.siblings('.value').html(response);
				datepicker();
				chosen();
				
				//обязательное поле или нет
				 $required = $('[name *= "param_' + $param_id + '"]').attr('required');
				
				//убираем кнопку "удалить" для обязательного поля
				if($required)
					$('.del').remove();
					
				//кнопка "ОТМЕНИТЬ"
				 $('#'+ $id).on('click', '.cancel',  function(){
					$this.siblings('.value').html($value);
					start_condition();
				}); 
				
				//кнопка "УДАЛИТЬ"
				$('#'+ $id).on('click', '.del',  function(){					
					if (confirm('Удалить параметр?')){
						$.post('https://' + $host + '/data/delete/' + $param_id, {entity:$entity, data_type:$datatype}, function(response){				
							$this.parent('.parametr').remove();
							start_condition();
						});
					}
				});
				
				//кнопка "ОТПРАВИТЬ"
				$('#'+ $id).on('click', '.edit',  function(){				
					var $this_edit = $(this);
					
					//файл или изображение
					if($datatype == 'img' || $datatype == 'file' ){						
						var $file_data = $this_edit.siblings('.ifile').prop('files')[0];
						if($file_data){
							var $ext = $file_data['name'].split('.').pop();
							var $filename = $entity + '_' + $param_id + '.' + $ext;
						
							var form_data = new FormData();							
							form_data.append('file', $file_data);                    
							form_data.append('entity', $entity );							
							form_data.append('data_type',$datatype);
							$.ajax({
								url: 'https://' + $host + '/data/edit/' + $param_id,
								datatype: 'text',
								cache: false,
								contentType: false,
								processData: false,
								data: form_data,
								type: 'post',
								success: function(respons){
									if(respons)                                         
										$this_edit.append('<div class=error>' + respons + '</div>');
									else{
										//тут надо вывести новый файл
										$href = '/upload/input/' + $filename + '?' + new Date().getTime();
										if($datatype == 'img')										
											$newfile ='<a href="' + $href + '" data-fancybox><img src="' + $href + '"></a>';
										else 
											$newfile ='<a href="' + $href + '" target="_blank">посмотреть файл</a>';
										
										$this.siblings('.value').html($newfile);
										start_condition();
									}
								}
							});
						}
						else
							$('.error').html('Файл не выбран');
					}					
					
					//для остальных типов полей
					else {				
						$value_edit = $('[name = "param_' + $param_id + '"]').val();
						
						if($datatype == 'multidiscrete'){
							$value_edit = $('[name = "param_' + $param_id + '[]"]').val();
							$value_select = $('[name = "param_' + $param_id + '[]"] option:selected').text();
						}				
						
						if($datatype == 'discrete' || $datatype == 'logical')
							$value_select = $('[name = "param_' + $param_id + '"] option:selected').text();						
						
						if($required && $value_edit==''){
							$('.error').html('Этот параметр обязателен для заполнения');
							$flag = 0;
						}
							
						if($flag){
							$.post('https://' + $host + '/data/edit/' + $param_id, {entity:$entity, data_type:$datatype, value:$value_edit}, function(response){				
								if(response){
									$('.error').html(response);
									//alert('ответ!');
								}
								else{
									if($datatype == 'discrete' || $datatype == 'multidiscrete' || $datatype == 'logical')
										$value_edit = $value_select;
									
									$('#'+ $id).children('.value').html($value_edit);
																	
									start_condition();
								}
							}); 
						}
					}
				});
			}
        }); 
	});

	
/** Проверка полей формы на заполненность	*/	
$(document).on('submit','#spr_form',function(e)	{
		//e.preventDefault();
		var $notvalid = 0;
		var $form = $(this);
		var $add_hidden = $('#add_hidden_form').html();
		var $value;
		$form.append($add_hidden);
		$form.find("input,select,textarea").each(function() {
			var $this = $(this);
			
			if($this.attr('required')){
				if(this.nodeName =='SELECT'){					
					$value = $this.find('option:selected').val();					
				} 
				if(this.nodeName =='INPUT' || this.nodeName =='TEXTAREA'){
					$value = $this.val(); 
				} 
					 
				if($value == "" || !$value ) {	
					$this.css({'border':'2px solid red'});
					$notvalid = 1;
				}
				/* else
					$this.css({'border':'1px solid silver'}); */
			} 
		});
		
		$('.symbol_field').each(function(){			
			var $this = $(this);
			
			if( $this.attr('required')){
				
				if($(this).html() == "") {				
					$(this).css({'border':'2px solid red'});
					$notvalid = 1;
				}
				/* else
					$(this).css({'border':'1px solid silver'}); */
			} 
		});
		
		if($notvalid==1) 			
			e.preventDefault();		
    });


	/* Выпадающее меню*/

	$('#navigation-menu > li').hover(function () {
		$(this).find('#submenu').show();
	}, function () {
		$(this).find('#submenu').hide();
	});



	$('.spr_table tbody > tr').on('click', function() {
		$(".spr_table tbody > tr").removeClass('blue-selected');
		$(this).addClass('blue-selected');
	});


	/*Адаптация таблиц*/
	$('table.spr_table').cardtable();



    /*мобильное меню*/


    $(function(){
        $('button.navbar-toggle').click(function(){
            $('ul#navigation-menu').toggleClass('collapse');

        });
    });

    $(function(){
        $('i.ddl-switch.fa.fa-angle-down').click(function(){
            $('li.dropdown').toggleClass('dd_active');

        });
    });





    /** показать/скрыть форму  */
/**
$( ".title_form_structure" ).click(function() {
  $( ".getform" ).toggle();
});
*/

/** Кнопка генерирования пароля*/
//$('form.user').find('input[name="password"]').after('<input type=button value=сгенерировать id=generate_pass>');	
	
/** Генериование пароля */
/* $('form.user').on('click', '#generate_pass',  function(){
       var $arr = [0,1,2,3,4,5,6,7,8,9,'a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
       $pass = '';       
        $arrsort = $arr.sort(function(){ return 0.5-Math.random() });
        $arrsort.forEach(function(item, index, array) {
            if(index<=8)
                $pass = $pass + item;
        });
        
        $(this).prev().val($pass);
        
	}); */

 
});
