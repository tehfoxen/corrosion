<?
//var_dump($data['param_search']);
//выбор параметров для поиска
echo '
<div id=datablock>
	<button class=paramsearch id=choose_param_add>Добавить параметры в поиск</button>
	<button class=paramsearch id=edit_param_val>Изменить значения параметров</button>
	<button class=paramsearch id=stat_prepare>Статистический анализ</button>
	<button class=paramsearch id=stat_result>Провести анализ</button>
	<div class=sblock1>';
		if(isset($data['param_search'])){
			echo '
			<form action="/search/chooseparam" method=post id=spr_form name=chooseparam>
				<select name="choose_param[]" id=choose_param multiple=multiple>';
				foreach($data['param_search'] as $category=>$value){
					echo '<optgroup label="'.$category.'">';
					foreach ($value as $k=>$array){
						//марку материала по умолчанию в правый селект
						$selected = $array[0] == 11 ? 'selected' : '';
						$explode = explode('@',$array[1]);						
						$class = isset($explode[1]) ? ' class=isdata ' : ' disabled=disabled ' ;
						echo '<option '.$selected.$class.' value='.$array[0].'>'.$explode[0].'</option>';
					}
					echo '</optgroup>';
				}
			echo '
				</select>
				<input type=submit value="дальше" />
			</form>';
		}
	echo '
	</div>
	<div class=sblock2></div>
	<div class=sblock3></div>
</div>';
