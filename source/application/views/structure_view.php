<?
echo '<h1 class="spr_title">'.$data['title'].'</h1>';
echo '<div class=line>';  
       
        echo '<div class=getform >';
            echo $data[0];
			if(route::arg()) 
				echo '<br><a class=add href="/'.route::controller_name().'/add">Добавить новое</a>';
        echo '</div>';   


    echo '<div>';
        echo $data[1];
    echo '</div>';

echo '</div>';