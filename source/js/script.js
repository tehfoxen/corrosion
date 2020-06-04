$(document).ready(function() {

$parsurl = window.location.pathname.split('/');
var $host = window.location.hostname;
var $controller_name = $parsurl[1];
var $action_name = $parsurl[2];
var $referrer = document.referrer;

/** ФУНКЦИЯ проверка на заполненность обязательных полей и на корректность введенных дат */
function CheckForm($form){
		var $notvalid_r = 0;
		var $notvalid_d = 0;
		
		$notvalid_r  = CheckFormEmpty($form);
		$notvalid_d = validDate($form);
				
		$notvalid = ($notvalid_d == 1 || $notvalid_r == 1) ? true : false;
		
		return $notvalid;
}


/** ФУНКЦИЯ Проверка обязательных полей формы на заполненность. На случай, если браузер не умеет. И на случай когда атрибут required у div элементов */
function CheckFormEmpty($form){
		var $notvalid = 0;		
		var $value = '';
		$form.find("input,select,textarea,div").each(function() {
			var $this = $(this);			
			if($this.attr('required')){				
				$value = $this.val();
				
				if(this.nodeName =='DIV'){
					//плагин Choosen
					if($this.hasClass('chosen-container')){
						$value = $this.children('a.chosen-single').find('span').text();
						$value = $.trim($value);
					}
					//символьное поле (для единиц измерения, например г/м²)
					if($this.hasClass('symbol_field'))
						$value = $this.html();
				}
					 
				if($value == "" || typeof $value == "undefined") {	
					$this.addClass('error_border');
					$notvalid = 1;
				}
			} 
		});
		
		return $notvalid;
}

/** ФУНКЦИЯ проверка даты на корректность. Подразумевается, что формат даты dd.mm.yyyy */
function validDate($form){
	var $notvalid = 0;	
	$form.find('.idate').each(function() {
		var $this = $(this);		
		var arrD = $this.val().split(".");
		arrD[1] -= 1;
		var d = new Date(arrD[2], arrD[1], arrD[0]);
		if ((d.getFullYear() == arrD[2]) && (d.getMonth() == arrD[1]) && (d.getDate() == arrD[0])) 
			$notvalid = 0;
		else {
			$notvalid = 1;
			$this.addClass('error_border');
		}
	});
	return $notvalid;
}


/** ПЛАГИН multiselect.js http://loudev.com/ (выбор значений из одного мультиселекта в другой )Для поиска по селекту требуется библиотека jquery.quicksearch.js*/
	function twoSelect($selector){
		$($selector).multiSelect({ 
			selectableOptgroup: true ,
			selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='поиск'>",
			selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='поиск'>",
			afterInit: function(ms){
				var that = this,
					$selectableSearch = that.$selectableUl.prev(),
					$selectionSearch = that.$selectionUl.prev(),
					selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
					selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';
				that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
				.on('keydown', function(e){
				  if (e.which === 40){
					that.$selectableUl.focus();
					return false;
				  }
				});
				that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
				.on('keydown', function(e){
				  if (e.which == 40){
					that.$selectionUl.focus();
					return false;
				  }
				});
			},
			afterSelect: function(){
				this.qs1.cache();
				this.qs2.cache();
			},
			afterDeselect: function(){
				this.qs1.cache();
				this.qs2.cache();
			}
		});
	}
twoSelect('#choose_param');


/** ПЛАГИН jQuery:MultiSelect https://github.com/nobleclem/jQuery-MultiSelect (чекбокы в списках множественного выбора)*/
	$('select.check').multiselect_check({
		columns  : 1,
		search   : true, 
		maxWidth :700,
		selectAll: true,
		texts    : {
			placeholder: 'Выберите методы',
			selectedOptions: ' выбрано',
			selectAll      : 'Выбрать всё',     
            unselectAll    : 'Снять выбор',
			search         : 'Поиск',
		}
	});   

 
/** ПЛАГИН Сортировка таблиц.  tablesorter2.min.js  https://mottie.github.io/tablesorter/docs/ */
function tablesorter($tables){
	$($tables).tablesorter({dateFormat : "ddmmyyyy",cssHeader:'tablesorter_all_theme',cssNoSort: 'tablesorter-noSort'}); 
}

tablesorter('#parametr, #method, #discrete, #user, #early, #searchresult');


$('#mydata').tablesorter({dateFormat : "ddmmyyyy",cssHeader:'tablesorter_all_theme',cssNoSort: 'tablesorter-noSort', headers: {
      0: { sorter: "shortDate" }   
    }});


/** ПЛАГИН Фискированные заголовки таблиц http://mkoryak.github.io/floatThead/*/
$('#parametr,#searchresult,#mydata').floatThead();

/** ПЛАГИН Календарь jquery UI Datepicker */	
function datepicker(){
	$('.idate').datepicker({
		changeMonth: true,
		changeYear: true,
		yearRange: "-62:+2"
	}); 
	 /** ПЛАГИН маска на поле  */
	 $(".idate").mask("99.99.9999");
};

datepicker();

/** ПЛАГИН Chosen - harvesthq.github.io/chosen Поиск в селекте. Подключаем для селектов, в которых список больше 10*/
function chosen(){
	$('select').each(function(indx, element){	
		if($(element).find('option').length>=15 && !$(element).attr('multiple') ){
			$width = $(element).width()+11;
			$(element).chosen({no_results_text: "Не найдено", placeholder_text_single: " ", width: $width, allow_single_deselect:true});
						
			if($(element).attr('required') == 'required'){
				$(element).next('.chosen-container-single').attr('required','required');
			}
		}
	});
};

chosen();

/** Общие скрипты */

//Удобная отмена загрузки файлов
$('body').on('change', 'input:file', function(){			
	$(this).after('   <span class="aslink remove_selected_file">удалить</span>');
});
$('body').on('click', '.remove_selected_file', function(){		
	$input = $(this).parent('div').find('input');
	$input.replaceWith($input.val('').clone(true));
	$(this).remove();
});


// Стиль для обязательных полей 	
$("input,select,textarea,div").each(function(){	
	if($(this).attr('required'))
        $(this).addClass('required');
});

/** ВХОД. Убрать легенду из формы авторизации */
$('fieldset.login legend').remove();


/** СТРУКТУРА. показать/скрыть форму добавления данных */
$( ".title_form_structure" ).click(function() {
	$( ".getform" ).show(); 
	$(window).scrollTop(2);
	$(this).hide();
});

if($action_name == 'add' && $referrer.split('/').includes('edit') ){
	$(".title_form_structure").trigger('click');	
}
if($action_name == 'edit' ){
	$(window).scrollTop(2);	
}


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

/** СТРУКТУРА. Показываем и скрываем поля "Множественный"  и "Единица измерения" */
if($controller_name == 'parametr'){			
	$('select[name=datatype_id]').on('change', function(){
	
		var $multiselect_field = $(this).parents('form').find('[name=multiselect]').parent('div');
		var $unit_id_field = 	 $(this).parents('form').find('[name=unit_id]').parent('div');

		$multiselect_field.hide();
		$unit_id_field.hide();
		
		if($(this).val() == 8)
			$multiselect_field.show();
		if($(this).val() == 1 || $(this).val() == 2)
			$unit_id_field.show();	
	});
	
	$('select[name=datatype_id]').trigger('change');
}

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
        $.post('http://' + $host + '/input/start/' + $this.val(), {}, function(response){
			if(response){
				$('.iblock1').html(response);
				tablesorter("#early"); 
				twoSelect('#choose_param');
			}
        });
    }
});

/** ВВОД ДАННЫХ. выбор из селекта gostlist на странице */
$('body').on('change', 'select.gostlist', function(){
    var $this = $(this);    
    if($this.val()){
        $.post('http://' + $host + '/input/gostlist/' + $this.val(), {}, function(response){
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
        $.post('http://' + $host + '/input/methodlist/' + $this.val(), {}, function(response){
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

/** ВВОД ДАННЫХ. Отправка выбранных параметров на странице и получение формы */
$(".iblock1").on("submit", "form.paramlist",function(e){    
 		e.preventDefault(); //отмена события по умолчанию. в данном случае сабмита формы
		
		$form_data = $(this).serialize();		
		if($form_data.length == 0){
			alert('Выберите параметры для создания формы внесения данных');
			return false;
		}			
		
		$('.iblock1 form.paramlist').hide();			
		$.post('http://' + $host + '/input/paramlist',$form_data,function(response){
			if(response){
				$('.iblock3').html(response);
				datepicker();
				chosen();
				//убираем атрибут required c тех select-ов, где подцеплен плагин choosen. Плагин делает оригинальное поле невидимым. Однако браузер на невидимое поле с атрибутом required ругается.
				$('.chosen-container').prev('select:hidden').removeAttr('required');
			}
		});
		
});

/** ВВОД ДАННЫХ. Отправка ранее введенного испытания. Отправляется id сущности. Получаем форму на основе параметров этой сущности  */
$(".iblock1").on("click", "#early td",function(){    		
		$('.iblock1 #early').hide();
			var $entity_id = $(this).attr('id');
			var $method_id = $(this).find('div').attr('data-method');
			var $testname = $(this).html();
			//console.log($entity_id);
			$.post('http://' + $host + '/input/earlyadded/' + $entity_id, function(response){
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
	$('.error_border').removeClass('error_border');
	var $form = $(this); 
	if(CheckForm($form))
		return false;
	
	//при нажатии кнопки "Отправить и внести еще образец"	
	if($press_submit=='many'){
		e.preventDefault();	
		
		$sample_param = $form.find('input[name=param_27]');
		$sample = $sample_param.val();
		
		$('[name=add_data]').hide();
		$('.loading').show();
		
		formData = new FormData($form.get(0));
		
		$.ajax({
		  url: 'http://' + $host + '/input/add/addmore',
		  type: 'POST',
		  data: formData,
		  contentType: false,
		  cache: false,
		  processData:false,
		  success: function(response) {
			/* console.log(response);		
			$('.input_anonce').show().html('Информация об образце <b>' + $sample + '</b> внесена. <br>Актуализируйте форму и отправьте данные о следующем образце');
			$sample_param.val('');				
			$('[name=add_data]').show();
			$('.loading').hide();
			$('body,html').animate({scrollTop: 0}, 100); */
			
			
			if(response)
				$('.input_anonce').show().addClass('error').html(response);			
			else{
				$('.input_anonce').show().removeClass('error').html('Информация об образце <b>' + $sample + '</b> внесена. <br>Актуализируйте форму и отправьте данные о следующем образце');
				$sample_param.val('');				
			}
				
			$('[name=add_data]').show();
			$('.loading').hide();
			$('body,html').animate({scrollTop: 0}, 100);
						
			
		  }
		});
    }
});

/** МОИ ДАННЫЕ, ПОИСК. открытие карточки образца (сущности) во всплывающем окне  */
$('#datablock, #searchresult, #mydata').on('click', 'a.fancybox',  function(e){
		e.preventDefault();
		var $this = $(this);
		var $url = $this.attr('href');
		
		$.post('http://' + $host + $url, {}, function(respons){
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
		}
		else{
			$this.html($title_edit);
			$this.attr('id','edit');
			$('.edit_value').hide();
			$('.entity').css({'background-color': 'white'});
		}
	});


function start_condition(){
	$('.parametr').css({'opacity':'1'});
	$('.edit_value,.entity_mode').show();
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
		$('.edit_value,.entity_mode').hide();
		
		$.post('http://' + $host + '/data/getfield/' + $param_id, {entity:$entity, data_type:$datatype, value:$value}, function(response){
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
						$.post('http://' + $host + '/data/delete/' + $param_id, {entity:$entity, data_type:$datatype}, function(response){				
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
								url: 'http://' + $host + '/data/edit/' + $param_id,
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
							$.post('http://' + $host + '/data/edit/' + $param_id, {entity:$entity, data_type:$datatype, value:$value_edit}, function(response){				
								if(response)
									$('.error').html(response);									
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

/** ПОИСК. Отправка выбранных параметров  */
$('.sblock1').on('submit','form[name=chooseparam]',function(e){
	e.preventDefault();	
	$.post('http://' + $host + '/search/chooseparam',$(this).serialize(),function(response){
		
		$('.sblock2').show().html(response);
		$('#choose_param_add').show();
		$('.sblock1').hide();
		$( ".choose_param_list" ).sortable();
	});
});


/** ПОИСК. Выбор из селекта (существует, не существует, равно...) */
$('.sblock2').on('change','select.query',function(){
	$this = $(this);
	$param_id = $this.attr('id');
	
	$this.parents('.choose_param .choose_select').children('.fieldforquery').remove();
	if($this.val()){
        $.post('http://' + $host + '/search/selectquery/' + $this.val(), {param_id:$param_id}, function(response){
			if(response){
				$this.parents('.choose_param .choose_select').append('<span class=fieldforquery>' + response + '<span>');
				datepicker();
				chosen();
			}
        });
    }
});

/** ПОИСК. Отправка введенных значений параметров. То есть, собственно поиск и вывод результатов. */
$('.sblock2').on('submit','form[name=search]',function(e){
	e.preventDefault();
	$.post('http://' + $host + '/search/search',$(this).serialize(),function(response){
		$('.sblock3').html(response);
		tablesorter('#searchresult');
		$('#datablock').trigger("click");
		$('#edit_param_val, #stat_prepare').show();		
		$('.sblock2').hide();
	});
});

/** ПОИСК. Клик по кнопке Изменить значения параметров */
$('#edit_param_val').click(function(){
	$('.sblock2').show();
	$('#edit_param_val, #stat_result').hide();	
});

/** ПОИСК. Клик по кнопке Добавить параметры */
$('#choose_param_add').click(function(){
	$('.sblock1').show();
	$('.sblock2, #choose_param_add, button.paramsearch').hide();
	$('.sblock3').html('');
	//$('#choose_param_add, #edit_param_val').hide();	
});

/** ПОИСК. Удаление параметра из поиска */
$('.sblock2').on('click','.choose_param .del',function(){
	//удаление из sblock2
	$paramid = $(this).parent('.choose_param').attr('id'); 
	$(this).parent('.choose_param').remove();	
	//удаление из sblock1
	$('#choose_param').multiSelect('deselect', $paramid);
});


/** ПОИСК. СТАТ-АНАЛИЗ. 
Клик по кнопке Статистический анализ. Подготовка к анализу. Вывод чекбоксов и селектов с методами расчета*/
$('#stat_prepare').click(function(){			
	//добавляем строки с селектами статистических методов
	var $attr_id = '';	
	$paramid_array = [];
	$('#searchresult th').each(function(i){
		var $paramid = 0;
		$attr_id = $(this).attr('id');
		if($attr_id)
			$paramid = $attr_id.split('_')[1];		
		$paramid_array.push($paramid);		
	});

	$.post('http://' + $host + '/stat/statprepare/', {paramid_array:$paramid_array}, function(response){
		if(response){
			$('#searchresult tbody').prepend('<tr id=trstat>' + response + '</tr>');				
			
			//чекбоксы в селекты. плагин
			$('select.stat_method').multiselect_check({
				columns  : 1,
				maxWidth : 200,
				minHeight: 150,
				texts    : {
					placeholder: 'Выберите методы',
					selectedOptions: ' выбрано',
				}
			}); 

			//добавляем столбцы с чекбоксами
			$('#searchresult tr').prepend('<td><input type="checkbox" checked class=column></td>');
			$('#searchresult tr:first td:first').css({'border-top':'none','border-left':'none'}).html('');
			$('#searchresult tr:eq(1) td:first input').addClass('check_all');
			$('.check_all').attr('title','Выбрать всё/снять выбор');
			$('#stat_prepare').hide();
			$('#stat_result').show();
			//$('#searchresult tbody tr:first td').css({'border-bottom':'2px solid silver'});
		}			
		else
			alert('Статистический анализ проводится только с числовыми данными');
	});
});


/** ПОИСК. СТАТ-АНАЛИЗ. 
клик по кнопке Провести анализ. Считаем всё.*/
$('#stat_result').click(function(){
	var $param_array = new Object();
	var $stat_array = new Object();
	var $td_val = [];	
	var $ischeck = true;
	var $method_arr = [];
	var $result = new Object();
	$selected_method = 0;
	$checked_row = 0;
	
	//поверка выбран ли метод анализа хоть один
	$('#searchresult select').each(function(){
		$selected_method += $("option:selected", this).length;		
	});
	
	if($selected_method == 0){
		alert('Выберите метод анализа');
		return false;
	}		
	
	//проверка выбраны ли числовые данные (больше 1)
	$('#searchresult .column:not(.check_all)').each(function(){
		if($(this).prop('checked'))
			$checked_row += 1;		
	});
	
	if($checked_row < 2){
		alert('Выберите данные для анализа');
		return false;
	}		
	
	$('#searchresult tbody tr').each(function(e){
		
		$(this).find('td').each(function(i){
			if(e==0){
				if(typeof $(this).attr('id') === "undefined")
					return;				
				
				$paramid = $(this).attr('id');
				$param_array[i] = $paramid;				
				
				$stat_array[$paramid] = {};	
				$method_arr = $(this).children('select').val();
				if($method_arr.length>0)
					$td_val.push(i);
				
				$stat_array[$paramid][e] = $method_arr;
			}
			if(e>0){
				if(i==0){
					$ischeck = $(this).children('.column').prop('checked');
				}
				if($.inArray(i, $td_val)!='-1' && $ischeck){					
					
					$value = $(this).text();
					$param = $param_array[i];	
					$stat_array[$param][e] = $value;
				}
			}
		});
	});
	
	$.post('http://' + $host + '/stat/statresult/', {stat_array:$stat_array}, function(response){
	
		$result = JSON.parse(response);
		$('.statresult').remove();
		
		var $method_name = '';
		for(var key in $result){
			var $method_result = $result[key];
			var $str = '<div class=statresult>';			
			for(method_key in $method_result){				
				$method_name = $('select.stat_method option[value='+method_key+']').html();	
				$method_name = $.trim($method_name);
				$str += '<div>' + $method_name + ': <span>' + $method_result[method_key] + '</span></div>';				
			}
			$str += '</div>';
			$('#searchresult tbody td#' + key).append($str);
		}
	});
});

/** ПОИСК. СТАТ-АНАЛИЗ. 
Клик по чекбоксу "выделить все" */
$('.sblock3').on('change', '.check_all', function(){	
	//если поставили галку - ставим везде
	if($(this).prop("checked"))
		$('.column').prop("checked", true);
	//если сняли галку - снимаем везде
	else
		$('.column').prop("checked", false);
});

/** ПОИСК. СТАТ-АНАЛИЗ. 
Клик по любому чекбоксу .column кроме .check_all */
$('.sblock3').on('change', '.column:not(.check_all)', function(i){	
	var $notcheck = 0;
	//если ставим галку, то смотрим, а не стоит ли она у всех. Если стоит, то ставим галку и у .check_all
	if($(this).prop("checked")){
		$('.column').not('.check_all').each(function(){
			if($(this).prop("checked")==false)
				$notcheck = 1;
		});
		if($notcheck == 0)
			$('.check_all').prop("checked",true);		
	}
	//если снимаем галку, то снимаем и с .check_all
	else
		$('.check_all').prop("checked",false);
});

	
/** Проверка полей формы на заполненность	*/	
$(document).on('submit','#spr_form:not(.input_data)',function(e){	
	if(CheckForm($(this)))
		return false;	
});

 
});
