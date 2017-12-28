<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/chosen-bootstrap/chosen/chosen.jquery.min.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/chosen-bootstrap/chosen/chosen.css"); ?>
<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <? $attribute['htmlOptions']['class'] .= 'chosen span12'; ?>
        <? $attribute['htmlOptions']['multiple'] .= 'multiple'; ?>
        <?php echo $form->dropDownList($model, $field, $attribute["items"], $attribute['htmlOptions']); ?>
    </div>
</div>