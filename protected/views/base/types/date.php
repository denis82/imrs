<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/bootstrap-datepicker/css/datepicker.css"); ?>  
<? $this->clientScript->registerScript("date-picker", "$('.date-picker').datepicker({language:'ru'});"); ?>
<? $attribute['htmlOptions']['class'] .= 'm-wrap m-ctrl-medium date-picker'; ?>
<div class="control-group <? if ($model->hasErrors($field)): ?> error<? endif; ?>">    
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <div class="input-append date date-picker" data-date-format="dd.mm.yyyy">
            <?php echo $form->textField($model, $field, $attribute['htmlOptions']); ?>            
            <span class="add-on"><i class="icon-calendar"></i></span>
        </div>
    </div>
</div>