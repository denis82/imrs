<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>



<div class="panel panel-flat">
	<div class="panel-body form-horizontal">
		<?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal jOrgForm', 'enctype' => 'multipart/form-data'))); ?>

		<? if ( isset($errors) && count($errors) ): ?>
			<div class="alert alert-error bg-danger-700">
				<p>
					<? foreach ( $errors as $error ): ?>
						<?php echo $error[0] ?><br/>
					<? endforeach; ?>
				</p>
			</div>
		<? endif; ?>

		<div class="form-group pt-15">
			<label class="col-lg-2">
				<?php echo $modelProject->getAttributeLabel('error_control') ?>
			</label>
			<div class="col-lg-10">
				<!--<input type="checkbox" class="styled" checked="checked">-->
				<?php echo $form->checkBox($modelProject, 'error_control', array('class' => 'styled')); ?>
			</div>
		</div>
		
		<div class="form-group pt-15">
			<label class="col-lg-2">
				<?php echo $errorForm->getAttributeLabel('sitemap') ?>
			</label>
			<div class="col-lg-10">
				<!--<input type="checkbox" class="styled" checked="checked">-->
				<?php echo $form->textField($errorForm, 'sitemap', array('class' => 'form-control', "maxlength" => 250)); ?>
			</div>
		</div>
		
		<div class="form-group pt-15">
			<label class="col-lg-2">
				<?php echo $errorForm->getAttributeLabel('sitemap_status') ?>
			</label>
			<div class="col-lg-10">
				<!--<input type="checkbox" class="styled" checked="checked">-->
				<?php echo $form->checkBox($errorForm, 'sitemap_status', array('class' => 'styled')); ?>
			</div>
		</div>
		
		<div class="form-group pt-15">
			<label class="col-lg-2">
				<?php echo $errorForm->getAttributeLabel('robots') ?>
			</label>
			<div class="col-lg-10">
				<!--<input type="checkbox" class="styled" checked="checked">-->
				<?php echo $form->textField($errorForm, 'robots', array('class' => 'form-control', "maxlength" => 250)); ?>
			</div>
		</div>
		
		<div class="form-group pt-15">
			<label class="col-lg-2">
				<?php echo $errorForm->getAttributeLabel('robots_status') ?>
			</label>
			<div class="col-lg-10">
				<!--<input type="checkbox" class="styled" checked="checked">-->
				<?php echo $form->checkBox($errorForm, 'robots_status', array('class' => 'styled')); ?>
			</div>   
		</div>
		
		
		
		<div class="form-group">
			<label class="control-label col-lg-2">
				<?php //echo $errorForm->getAttributeLabel('path_to_form') ?>
			</label>
			<div class="col-lg-10">
			      <?php// echo $form->textArea($errorForm, 'path_to_form', array('class' => 'form-control', 'rows' => '5', 'cols' => '5')); ?>
			</div>
		</div> 
	</div>
</div>





<div class="panel panel-flat">
	<div class="panel-body form-horizontal">
		<?php //$form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal jOrgForm', 'enctype' => 'multipart/form-data'))); ?>

		<? if ( isset($errors) && count($errors) ): ?>
			<div class="alert alert-error bg-danger-700">
				<p>
					<? foreach ( $errors as $error ): ?>
						<?php echo $error[0] ?><br/>
					<? endforeach; ?>
				</p>
			</div>
		<? endif; ?>
		
		<!-- Basic alert -->

		<? if ( $errorForm->showInfo ): ?>
			<div class="alert alert-info alert-styled-left">
				    <button type="button" class="close" data-dismiss="alert"><span>&times;</span><span class="sr-only">Close</span></button>
				    <p><?php echo $errorForm->getAttributeLabel('allCountUrlString') ?> - <?php echo $errorForm->allCountUrlString; ?><p>
				    <p><?php echo $errorForm->getAttributeLabel('countUrlString') ?> - <?php echo $errorForm->allCountUrlString-$errorForm->damageCountUrlString; ?><p>
				    <p><?php echo $errorForm->getAttributeLabel('damageCountUrlString') ?> - <?php echo $errorForm->damageCountUrlString; ?><p>
				    <p><?php echo $errorForm->getAttributeLabel('damageUrl') ?><p>
				    <? foreach ( $errorForm->damageUrl as $url ): ?>
						<hr>
						<?php echo $url['path']; ?><br/>
						<p><?php echo "code: " . $url['status'];?></p>
						
				    <? endforeach; ?>
			</div>
		<? endif; ?>
	
		<!-- /basic alert -->
		
		<div class="form-group">
			<label class="control-label col-lg-2">
				<?php echo $errorForm->getAttributeLabel('path_to_form') ?>
			</label>
			<div class="col-lg-10">
			      <?php echo $form->textArea($errorForm, 'path_to_form', array('class' => 'form-control', 'rows' => '20', 'cols' => '5')); ?>
			</div>
		</div> 
		<div class="row">
			<div class="col-lg-6 col-md-6 text-left">
				<a href="<?= Yii::app()->urlManager->createUrl('project/index/update', array('id' => $modelProject->domain_id)) ?>" class="btn btn-danger">
					<i class="icon-arrow-left8"></i> 
					Отмена
				</a>
			</div>
			<div class="col-lg-6 col-md-6 text-right">
				<button type="submit" class="btn btn-success">
					<i class="icon-checkmark4"></i> 
					Сохранить
				</button>
			</div>
		</div>                 
		<?php $this->endWidget(); ?>
	</div>
</div>

