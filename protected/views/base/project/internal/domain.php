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

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title text-semiold">Информация о домене</h5>

                <div class="heading-elements">
                    <span class="heading-text jLastUpdate">
                    	<?= $last_update ? '<i class="icon-history position-left text-success"></i> ' . TxtHelper::DateTimeFormat( $last_update ) : '' ?>
                    </span>

                    <ul class="icons-list">
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>                
            </div>
            <div class="panel-body">
                <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'whois')) ?>">
                    <span class="icon-spinner4 spinner"></span>
                </div>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title text-semiold">Скриншот</h5>

                <div class="heading-elements">
                    <span class="heading-text jLastUpdate">
                    </span>

                    <ul class="icons-list">
                        <li><a data-action="reload"></a></li>
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>                
            </div>
            <div class="panel-body">
                <? if ($screenshot): ?>
                    <img src="<?= $screenshot->image ?>" style="max-width: 100%; height: auto;">
                <? else: ?>
                    <div class="icon-spinner4 spinner"></div>
                <? endif; ?>

                <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'screenshotDate')) ?>">
                </div>
            </div>
        </div>

        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title text-semiold">История</h5>
            </div>
            <div class="panel-body">
                <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'wayback')) ?>">
                    <span class="icon-spinner4 spinner"></span>
                </div>
            </div>
        </div>

    </div>

</div>

