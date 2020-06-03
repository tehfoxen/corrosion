<?
/** 
для названия таблицы:
знач1@знач2@знач3
знач1 [0] - название таблицы
знач2 [1] - сортировка таблицы при выводе. Выражение для ORDER BY
знач3 [2] - search  - форма поиска. Основана на виртуальной таблице (реально в БД не существующей)


для полей:
знач1@знач2@знач3@знач4
знач1 [0] - название поля в форме или таблице
знач2 [1] - тип поля. 
	- text:			обычное текстовое поле (текст, числа)
	- textarea		расширенное текстовое поле. Отличается от text только в визуализации
	- select:		выпадающий список значений из другой таблицы
	- symbol:		поле, обрабатывающее тэги верхнего и нижнего регистра
	- selectmulti:	выпадающий список с множественным выбором
	- data:			поле с датой, к которому подгружается календарик	
	- file:			загрузка файлов
	- autocomplete  после автоподстановки значений по мере ввода. Autocomplete Widget (jquery). В php-скрипте вывода формы редактирования к этому полю добавится класс autocomplete_field, к которому нужно будет привязать вызов плагина autocomplete в script.js и обработку в ajax.php (если нужно)
    - logical       1 или 0 (ДА/НЕТ)
	
знач3 [2] - обязательно ли поле для заполнения. Если required - обязательно.

знач4 [3] - видимость поля в форме. 
	- disabled:	    выводится в форме заблокированным
	- auto:			не видно в форме. Auto формируются на основании других полей и даже других таблиц. 

знач5 [4] - видимость поля в базовой таблице
	- hidden:		поле в таблице не выводится
	- link:			поле в виде ссылки. 
	
знач6 [5] - запрос where для поля с типом select

*/
$tbl_array = array(

'user'	=> array(
		'Пользователи@`create_time` DESC', 
		'name'=>'Имя@text@required@@link',
        'email'=>'Email@text@required',        
		'role_id'=>'Роль@select@required',
		'email_status'=>'Подтвержден@logical@@auto',
        'create_time'=>'Зарегистрирован@data@@auto'),
'category' => array (
        'Категории@`order` ASC',
        'name'=>'Название@text@required',
        'order'=>'Порядок@text@required'),
        
'gost' => array (
        'ГОСТ',
        'name'=>'Название@text@required',
        'file'=>'Файл@file'),
        
'method' => array(
        'Методы испытаний',
        'name'=>'Название@text@required',
        'gost_id'=>'Гост@select@required'),
        
'unit' => array(
        'Единицы измерения',
        'name'=>'Размерность@symbol@required'), 

'parametr' => array(
        'Параметры',
        'name'=>'Название@text@required',    
        'datatype_id'=>'Тип данных@select@required',
		'multiselect'=>'Множественный@logical@@@hidden',
        'unit_id'=>'Единица измерения@select',
        'category_id'=>'Категория@select@required',
        'method_id'=>'Метод испытаний@selectmulti@@@hidden',
        'required'=>'Обязательный для заполнения@logical',
        'pattern'=>'Столбец таблицы раздела "Мои данные"@logical',		
		'search'=>'Столбец таблицы быстрого поиска@logical',
		'access'=>'Виден всем@logical'
        ),        
'discrete' => array(
        'Списки@parametr_id ASC, `order` ASC',
        'name'=>'Название@text@required',
        'parametr_id'=>'Дискретный параметр@select@required@@@datatype_id=8',
        'order'=>'Порядок@text'), 
        
'login'	=> array(
		'Авторизация',
        'email'=>'Email@text@required',
        'password'=>'Пароль@text@required')		

);
?>