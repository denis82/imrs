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
        <h5 class="panel-title text-semiold">Структура сайта</h5>

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
    	<div class="tabbable">
    		<ul class="nav nav-tabs nav-tabs-bottom">
    			<li class="active"><a href="#s-sitemap" data-toggle="tab" aria-expanded="false">Sitemap</a></li>
    			<li class=""><a href="#s-crawler" data-toggle="tab" aria-expanded="false">Внутренние ссылки</a></li>
    			<li class=""><a href="#s-yandex" data-toggle="tab" aria-expanded="false">Яндекс</a></li>
    		</ul>

			<div class="tab-content">
				<div class="tab-pane active" id="s-sitemap">
			        <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'sitemap')) ?>">
			            <span class="icon-spinner4 spinner"></span>
			        </div>
				</div>

				<div class="tab-pane" id="s-crawler">
			        <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'crawlerstruct')) ?>">
			            <span class="icon-spinner4 spinner"></span>
			        </div>
				</div>

				<div class="tab-pane" id="s-yandex">
			        <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'yastruct')) ?>">
			            <span class="icon-spinner4 spinner"></span>
			        </div>
				</div>
			</div>
		</div>

    </div>
</div>

