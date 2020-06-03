<?
var_dump($_REQUEST);
?>

<form method=post>
<input type=text name=sss />
<input type=submit value="отправить" />
</form>

<?
function validDate($date) { // проверка на правильность формата даты
    $d = DateTime::createFromFormat('Y-m-d', $date);	
	return $d && $d->format('Y-m-d') === $date;
}


var_dump(validDate('sdfgsdgsf'));
var_dump(validDate('02.12.2020'));
var_dump(validDate('2020-02-12'));

/* 
$date = new DateTime('2000-01-01');
echo $date->format('Y-m-d H:i:s'); */




?>