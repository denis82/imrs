<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>
<div class="panel panel-flat">
    <div class="panel-body form-horizontal">

    <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal jOrgForm', 'enctype' => 'multipart/form-data'))); ?>

        <? if(isset($errors) && count($errors)): ?>
            <div class="alert alert-error bg-danger-700">
                <p>
                    <? foreach($errors as $e): ?>
                        <?= $e[0] ?><br/>
                    <? endforeach; ?>
                </p>
            </div>
        <? endif; ?>

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $fdata->getAttributeLabel('site') ?></label>
            <div class="col-lg-10">
            	<?php echo $form->textField($fdata, 'site', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="row">
        	<div class="col-lg-6 col-md-6 text-left">
        		<a href="<?= Yii::app()->urlManager->createUrl('project/index/update', array('id' => $model->id)) ?>" class="btn btn-danger"><i class="icon-arrow-left8"></i> Отмена</a>
        	</div>
        	<div class="col-lg-6 col-md-6 text-right">
        		<button type="submit" class="btn btn-success"><i class="icon-checkmark4"></i> Сохранить</button>
        	</div>
        </div>                 

    <?php $this->endWidget(); ?>

    </div>
</div>

