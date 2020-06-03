<?
require_once 'core/model.php';
require_once 'core/view.php';
require_once 'core/controller.php';
require_once 'core/route.php'; 

function my_autoloader($name) {
    require_once 'classes/' . strtolower($name) . '.php';
	//require_once 'controllers/' . strtolower($name) . '.php';
}

spl_autoload_register('my_autoloader');



Route::start(); // запускаем маршрутизатор
