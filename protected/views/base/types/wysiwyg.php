<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/ckeditor/ckeditor.js"); ?>
<? $attribute['htmlOptions']['class'] .= 'span12 ckeditor m-wrap'; ?>
<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">        
        <?php echo $form->textArea($model, $field, $attribute['htmlOptions']); ?>
    </div>
</div>