<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/bootstrap-toggle-buttons/static/js/jquery.toggle.buttons.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/bootstrap-toggle-buttons/static/stylesheets/bootstrap-toggle-buttons.css"); ?>
<? $this->clientScript->registerScript("basic-toggle", "$('.basic-toggle-button').toggleButtons({ label: {
                enabled: 'Да',
                disabled: 'Нет'
            },
            style: {
                enabled: 'success',
                disabled: 'danger'
            }
            });"); ?>
<?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
<? $attribute['htmlOptions']['class'] .= 'toggle'; ?>
<div class="controls">
    <label class="basic-toggle-button">
        <?php echo $form->checkBox($model, $field, $attribute['htmlOptions']); ?>
    </label>    
</div>