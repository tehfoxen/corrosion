<?
class Model_Login extends Model
{
    public function GetUser(){
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $stmt = DB::run("SELECT `id`, `role_id`, `name`, `password` FROM `user` WHERE `email`=?", [$email]);
        $row = $stmt->fetch();
        $hash_bd = $row['password'];
        if(password_verify($password, $hash_bd)){
            $_SESSION['user_role'] = $row['role_id'];
            $_SESSION['user_name'] = $row['name']; 
			$_SESSION['user_id'] = $row['id']; 
            header('Location:/');
        }
        else 
            return 0;
    }
}