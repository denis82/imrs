<? $attribute['htmlOptions']['class'] .= 'm-wrap span12'; ?>
<div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <? ?>
        <div class="input-icon left">
            <i class="icon-<?= $attribute['icon'] ?>"></i>            
            <?php echo $form->textArea($model, $field, $attribute['htmlOptions']); ?>
        </div>        
        <span class="help-block"><?= $model->getError($field) ?></span>
    </div>
</div>
