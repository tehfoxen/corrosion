<?
class User{    
    public $mail_admin;
    
    public function __construct(){
        //$this->mail_admin = 'registration@'.route::host();
		$this->mail_admin = 'registration@viam.ru';
    }
   
   /** генерация парорля и хэша на его основе. */
   public function PasswordGenerate($max){
        $abc=array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
        $psw='';
        for ($i=1;$i<=$max;$i++)  {
            $rnd=rand(0,count($abc)-1); 
            $psw.=$abc[$rnd];
        }
       
        $hash = password_hash($psw, PASSWORD_DEFAULT);
        return [$psw,$hash];        
    }
	
    /** отправка письма с данными авторизации (после подтверждения) */
	public function SendRegMail($mail,$name,$password){
        $message="Здравствуйте, $name!<br><br>Вы зарегистрированы на сайте <a href='".route::hostlink()."'>База данных коррозионных испытаний</a> <br><br>
		Ваши данные для авторизации:<br>Логин: $mail<br>Пароль: $password<br><br>";
				
		$from		=	$this->mail_admin;
		$to			=	$mail;
		$subject	=	"Регистрация на сайте База данных коррозионных испытаний";	
		$mail_headers = "Content-Type: text/html; charset=utf-8 \r\n";
		//$mail_headers = "Content-type: text/html; charset=utf8 \r\n";
		$mail_headers .= "From: БД Коррозионных испытаний <$from>\r\n";

		//mail($to, $subject, $message, $mail_headers);
		mail($to, $subject, $message, iconv('utf-8', 'windows-1251', $mail_headers)); 
            
    }
	
	/** отправка письма с подтверждающей ссылкой */
	public function SendMailConfirm($mail,$name,$code){
        $message="Здравствуйте, $name!<br><br>Ваш адрес был зарегистрирован на сайте <a href='".route::hostlink()."'>База данных коррозионных испытаний</a> <br><br>
		Перейдите, пожалуйста, <a href='".route::hostlink()."login/confirm/$code'>по ссылке</a>, чтобы подтвердить регистрацию.";
				
		$from		=	$this->mail_admin;
		$to			=	$mail;
		$subject	=	"Подтверждение регистрации на сайте База данных коррозионных испытаний";	
		$mail_headers = "Content-Type: text/html; charset=utf-8 \r\n";
		$mail_headers .= "From: БД Коррозионных испытаний <$from> \n";
		mail($to, $subject, $message, iconv('utf-8', 'windows-1251', $mail_headers));            
    }
	
	/** первоначальная регистрация юзера, отсылка письма с кодом активации */
	public function UserReg($id){
		//генерим код активации
        $pass_arr = $this->PasswordGenerate(15);
        $code = $pass_arr[0];            
        //yqzzvg3tf для Листер
		
        //пишем хэш в БД
        $stmt = DB::run("UPDATE `user` SET `activation_code`=? WHERE id=?", [$code, $id]);
            
        //отсылаем письмо юзеру
        $row = DB::run("SELECT `name`, `email` FROM `user` WHERE id=?", [$id])->fetch();
        $mail = $row['email'];
        $name = $row['name'];
        $this->SendMailConfirm($mail,$name,$code);
	}
	
	/** окончательная регистрация юзера после подтверждения по ссылке в письме */
	public function UserRegFinally(){
		$activation_code = route::arg();
		
		$row = DB::run("SELECT `name`, `email`, `id` FROM `user` WHERE activation_code=?", [$activation_code])->fetch();
        if($row){
			$id = $row['id'];
			$mail = $row['email'];
            $name = $row['name'];
			//генерим пароль
            $pass_arr = $this->PasswordGenerate(9);
            $pass = $pass_arr[0];
            $pass_hash = $pass_arr[1];
            //пишем хэш в БД
            $stmt = DB::run("UPDATE `user` SET `password`=?, email_status=?, activation_code=? WHERE id=?", [$pass_hash, 1, NULL, $id]);			
            $this->SendRegMail($mail,$name,$pass);
			
			return 1;
		}
		else
			return 0;
	}
}
?>