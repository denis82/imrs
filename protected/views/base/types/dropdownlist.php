<?php
/**
 * @var array $attribute
 * @var CModel $model
 * @var string $field
 */
$attribute['htmlOptions']['class'] = isset($attribute['htmlOptions']['class'])? $attribute['htmlOptions']['class'] : '';
?>
<? $attribute['htmlOptions']['class'] .= 'm-wrap span12'; ?>
<div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <?php echo $form->dropDownList($model, $field, $attribute["items"], $attribute['htmlOptions']); ?>        
    </div>
</div>