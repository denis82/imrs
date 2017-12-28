<?

$tpl_name = explode('/', str_replace('.php', '', __FILE__));
$tpl_name = implode('/', array_slice($tpl_name, -3) );

$tpl = TplText::model()->findByAttributes(array('name' => $tpl_name));

if (!$tpl) {
	$tpl = new TplText;
	$tpl->name = $tpl_name;
	$tpl->save();
}

if ($tpl) {
	print '<div class="panel panel-flat"><div class="panel-body jEditablePanel" data-id="'. $tpl->id .'">'. $tpl->html .'</div></div>';
}

if (Yii::app()->user->role == 'administrator') {
	print '<div class="panel panel-flat"><div class="panel-body jStaffPanel" data-name="'. $tpl_name .'">';

	foreach (TplStaff::model()->findAllByAttributes(array('name' => $tpl_name)) as $j) {
		print 
			'<div class="jStaffItem" data-id="' . $j->id . '">' . 
				$j->staff->name . ' // ' . 
				$j->staff->price . 'р * ' . $j->timer . ' = ' . ($j->staff->price * $j->timer) . 
			'</div>';
	}

	print '</div></div>';
}

?>

<?

if ($result) {
	if ($result->value == 'yes') {
		?>
		<div class="alert alert-success alert-styled-left alert-bordered">
			Сайт доступен по HTTPS.
		</div>
		<?
	}
	else {
		?>
		<div class="alert alert-danger alert-styled-left alert-bordered">
			Ваш сайт не имеет HTTPS протокола передачи данных в зашифрованном виде, рекомендуем установить сертификат SSL и наладить обмен данными через HTTPS-протокол.
		</div>
		<?
	}
}
else {
	?>
	<div class="alert alert-info alert-styled-left alert-bordered">
		Информация не доступна.
	</div>
	<?
}