<h1 class="spr_title">Госты</h1>
<?
echo '<ul class=standart>';
foreach($data as $name=>$link){
    echo '<li><img src="'.$link[1].'" /><a href="'.$link[0].'" target=_blank>'.$name.'</a></li>';
}
echo '</ul>';
