<?
session_start();
define ('ROLE',$_SESSION['user_role']);
define ('NAME',$_SESSION['user_name']);

if($_SERVER['REMOTE_ADDR']=='127.0.0.1')    
	ini_set('display_errors', 1);
else
	ini_set('display_errors', 'off');

require_once 'application/bootstrap.php';
helper::WriteLog('activity');

//var_dump($_SERVER);