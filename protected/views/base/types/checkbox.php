<?php
/**
 * @var array $attribute
 * @var CModel $model
 * @var string $field
 */
$attribute['htmlOptions']['class'] = isset($attribute['htmlOptions']['class'])? $attribute['htmlOptions']['class'] : '';
// <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
?>
<div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <label class="checkbox">
            <?php echo $form->checkBox($model, $field, $attribute['htmlOptions']); ?>
        </label>
    </div>
</div>