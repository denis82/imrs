<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/lib/codemirror.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/addon/edit/matchbrackets.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/htmlmixed/htmlmixed.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/xml/xml.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/javascript/javascript.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/css/css.js"); ?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/clike/clike.js"); ?>    
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/codemirror/mode/php/php.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/codemirror/lib/codemirror.css"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/codemirror/theme/solarized.css"); ?>
<? $this->clientScript->registerScript("codemirror", 'var editor = CodeMirror.fromTextArea(document.getElementById("FMFile_content"), {lineNumbers: true, mode:"' . $attribute['mode'] . '", theme:"solarized dark", viewportMargin: Infinity });'); ?>
<? $attribute['htmlOptions']['class'] .= 'm-wrap span12'; ?>
<style type="text/css">
    .CodeMirror {
        border: 1px solid #eee;
        height: auto;
    }
    .CodeMirror-scroll {
        overflow-y: hidden;
        overflow-x: auto;
    }
</style>
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