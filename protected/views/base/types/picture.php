<?php
/**
 * @var array $attribute
 * @var CModel $model
 * @var string $field
 */
$attribute['htmlOptions']['class'] = isset($attribute['htmlOptions']['class'])? $attribute['htmlOptions']['class'] : '';
?>
<? $this->clientScript->registerScriptFile($this->assetsUrl . "/plugins/bootstrap-fileupload/bootstrap-fileupload.js"); ?>
<? $this->clientScript->registerCssFile($this->assetsUrl . "/plugins/bootstrap-fileupload/bootstrap-fileupload.css"); ?>
<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <div class="fileupload fileupload-new" data-provides="fileupload">
            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                <? if ($model->{$attribute['picture']}): ?>
                    <img src="<?= $model->{$attribute['picture']} ?>" alt=""  id="thumb_<?= CHtml::activeId($model, $attribute['picture']) ?>">                    
                <? else: ?>
                    <img src="http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image" alt="" id="thumb_<?= CHtml::activeId($model, $attribute['picture']) ?>">
                <? endif; ?>
            </div>
            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
            <div>
                <span class="btn btn-file mini blue">
                    <span class="fileupload-new"><i class="icon-upload icon-white"></i> Выбрать</span>                    
                    <span class="fileupload-exists"><i class="icon-upload icon-white"></i> Изменить</span>                    
                    <? $attribute['htmlOptions']['class'] .= 'default'; ?>
                    <?php echo $form->fileField($model, $field, $attribute['htmlOptions']); ?>
                </span>
                <? if ($model->{$attribute['picture']}): ?>
                    <a href="#" class="fileupload-new btn mini yellow" data-dismiss="fileupload" onclick="
                                $('#<?= CHtml::activeId($model, $attribute['picture']) ?>').val('');
                                $('#thumb_<?= CHtml::activeId($model, $attribute['picture']) ?>').attr('src', 'http://www.placehold.it/200x150/EFEFEF/AAAAAA&amp;text=no+image');
                                $(this).hide();
                                return false;"><i class="icon-ban-circle icon-white"></i> Удалить</a>
                       <?php echo $form->hiddenField($model, $attribute['picture']); ?>                    
                   <? endif; ?>
                <a href="#" class="btn fileupload-exists mini yellow" data-dismiss="fileupload"><i class="icon-ban-circle icon-white"></i> Удалить</a>
            </div>            
        </div>        
    </div>
</div>