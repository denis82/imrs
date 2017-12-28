<?php
/**
 * @var array $attribute
 * @var CModel $model
 * @var string $field
 */
$attribute['htmlOptions']['class'] = isset($attribute['htmlOptions']['class'])? $attribute['htmlOptions']['class'] : '';
?>
<? $attribute['htmlOptions']['class'] .= 'm-wrap span12'; ?>
<? if (!isset($attribute['icon'])): ?>
    <div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
        <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
        <div class="controls">        
            <?php echo $form->passwordField($model, $field, $attribute['htmlOptions']); ?>
            <span class="help-block"><?= $model->getError($field) ?></span>
        </div>
    </div>
<? else: ?>
    <div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
        <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
        <div class="controls">
            <? ?>
            <div class="input-icon left">
                <i class="icon-<?= $attribute['icon'] ?>"></i>            
                <?php echo $form->passwordField($model, $field, $attribute['htmlOptions']); ?>
            </div>        
            <span class="help-block"><?= $model->getError($field) ?></span>
        </div>
    </div>
<? endif; ?>