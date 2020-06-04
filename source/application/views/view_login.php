<?
if(!NAME){
    if(isset($data['confirm'])){
		if($data['confirm']==1)
			echo '<div class=successfully>Регистрация подтверждена. На почту отправлены данные авторизации. <a href="/login">Войти на сайт</a></div>';
		else
			echo '<div class=error>Неверный код активации либо регистрация была подтверждена ранее</div>';
	}
		
	if(isset($data['loginform']))
		echo $data['loginform'];
	
    if(isset($data['error']))
        echo '<div class=error>Нет такого пользователя</div>';
}	

?>


