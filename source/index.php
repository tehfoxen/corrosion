<?
//admin@corrosion
//yqzzvg3tf
session_start();
define ('ROLE',$_SESSION['user_role']);
define ('NAME',$_SESSION['user_name']);
    
//ini_set('display_errors', 1);
ini_set('display_errors', 'off');
require_once 'application/bootstrap.php';