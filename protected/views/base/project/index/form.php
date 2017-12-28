<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-flat">

            <div class="panel-body">
                <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'))); ?>

                    <? if(isset($errors) && count($errors)): ?>
                        <div class="alert alert-error bg-danger-700">
                            <p>
                                <? foreach($errors as $e): ?>
                                    <?= $e[0] ?><br/>
                                <? endforeach; ?>
                            </p>
                        </div>
                    <? endif; ?>

                    <div class="form-group">
                        <label class="control-label col-lg-2"><?= $model->getAttributeLabel('host') ?></label>
                        <div class="col-lg-10">
                            <?php echo $form->textField($model, 'host', array(
                                'class' => 'form-control', 
                                'required' => ($model->id ? '' : 'required'), 
                                'disabled' => ($model->id ? 'disabled' : ''), 
                                'placeholder' => 'http://seo-experts.com'
                            )); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-2"><?= $model->getAttributeLabel('name') ?></label>
                        <div class="col-lg-10">
                            <?php echo $form->textField($model, 'name', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-2"><?= $model->getAttributeLabel('regions') ?></label>
                        <div class="col-lg-10">
                            <?/*= $this->renderPartial('application.views.base.types.multiselect', array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true); */?>

                            <?php echo $form->dropDownList($model, 'regions', Region::getRegionsList(), array('class' => 'form-control')); ?>
                        </div>
                    </div>                    

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Сохранить <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                <?php $this->endWidget(); ?>

            </div>
        </div>
    </div>
</div>
