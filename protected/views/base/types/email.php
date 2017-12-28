<? $attribute['htmlOptions']['class'] .= 'm-wrap'; ?>
<div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <div class="input-icon left">
            <i class="icon-envelope"></i>            
            <?php echo $form->textField($model, $field, $attribute['htmlOptions']); ?>
        </div>        
        <span class="help-block"><?= $model->getError($field) ?></span>
    </div>
</div>