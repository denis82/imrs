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

<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Системы отзывов на сайте</h5>
    </div>
    <div class="panel-body">

    	<? if ($items and count($items)): ?>

		    			<? foreach ($items as $j => $i): ?>
		    				<div class="row">
		    					<div class="col-lg-3 col-md-3"><?= $j ?></div>
		    					<div class="col-lg-9 col-md-9">
		    						<? if ($i and is_array($i) and count($i)): ?>
		    							<? foreach ($i as $res): ?>
		    								<div>
		    									<?= nl2br(htmlspecialchars($res->html)) ?>
		    								</div>
		    							<? endforeach; ?>
		    						<? endif; ?>
		    					</div>
		    				</div>
		    			<? endforeach; ?>

    	<? else: ?>

    		<?= $this->renderPartial('//base/project/alert', array('style' => 'warning', 'text' => 'На сайте не найдено систем отзывов.')); ?>

    	<? endif; ?>

    </div>
</div>

