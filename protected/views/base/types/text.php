<?php
/**
 * @var array $attribute
 * @var CModel $model
 * @var string $field
 */
$attribute['htmlOptions']['class'] = isset($attribute['htmlOptions']['class'])? $attribute['htmlOptions']['class'] : 'form-control ';
?>
<? $attribute['htmlOptions']['class'] .= 'm-wrap span12'; ?>
<div class="form-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">
	<?php echo $form->labelEx($model, $field, array('class' => 'control-label col-lg-4')); ?>
		<div class="controls col-lg-8">
			<?if(isset($attribute['icon'])):?>
			<div class="input-icon left">
				<i class="icon-<?= $attribute['icon']; ?>"></i>
			<?endif;?>
                <?php echo $form->textField($model, $field, $attribute['htmlOptions']); ?>
			<?if(isset($attribute['icon'])):?>
			</div>
			<?endif;?>
		<span class="help-block"><?= $model->getError($field) ?></span>
	</div>
</div>