<?
if($_SERVER['REMOTE_ADDR']=='127.0.0.1'){
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'corrosion');
	define('DB_USER', 'root');
	define('DB_PASS', '');
	define('DB_CHAR', 'utf8');
}
else{
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'corrosion_test');
	define('DB_USER', 'corrosion_test');
	define('DB_PASS', '8V3x0Q9y');
	define('DB_CHAR', 'utf8');
}


class DB
{
    protected static $instance = null;

    public function __construct() {}
    public function __clone() {}

    public static function instance()
    {
        if (self::$instance === null)
        {
            $opt  = array(
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => TRUE,
            );
            $dsn = 'mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHAR;
            self::$instance = new PDO($dsn, DB_USER, DB_PASS, $opt);
        }
        return self::$instance;
    }
    
    public static function run($sql, $args = array())
    {
        $stmt = self::instance()->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }
	
	public static function __callStatic($method, $args)
    {
        return call_user_func_array(array(self::instance(), $method), $args);
    }

}
?>