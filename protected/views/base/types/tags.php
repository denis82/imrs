<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/select2/select2.min.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/select2/select2_metro.css"); ?>
<? $this->clientScript->registerScript("select", "$('#" . CHtml::activeId($model, $field) . "').select2({tags:[]});"); ?>

<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">        
        <? $attribute['htmlOptions']['class'] .= 'select2 select2-offscreen span12'; ?>
        <?php echo $form->hiddenField($model, $field, $attribute['htmlOptions']); ?>
    </div>
</div>