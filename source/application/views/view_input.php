<!--<h1 class="spr_title">Ввод данных</h1>-->

<?
if(!empty($data))
	echo '<div class=successfully>'.$data.'</div>';
else{ ?>
	<div class=input_select>
		<select class=start>
			<option value="" disabled selected>Выберите тип </option>
			<option value=1>Испытания в соответствии с ГОСТ</option>
			<option value=2>Испытания по программе испытаний  (не соответствует ГОСТ)</option>
			<option value=3>Ввод на основе введенных ранее данных</option>
		</select>
		<div class=iblock1></div>
		<div class=iblock2></div>
		<div class=iblock3></div>
	</div>
<?}
