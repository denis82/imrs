<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
        <div class="thumbnail" style="width: 600px; height: 450px;">
            <img src="<?= $model->$field ?>">
        </div>
    </div>
</div>