<? if ($model): ?>
    <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/index/load", array('id' => $project->id, 'method' => 'status')) ?>">
		<div class="alert alert-info alert-styled-left alert-bordered">
	        <span class="icon-spinner4 spinner"></span>
	        &nbsp; <span class="jStage"><?= $model->stageDesc() ?></span>
	    </div>
	</div>
<? else: ?>
	<div class="alert alert-success alert-styled-left alert-bordered">
		Проверка завершена.
	</div>
<? endif; ?>