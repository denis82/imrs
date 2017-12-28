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
                        <label class="control-label col-lg-4"><?= $model->getAttributeLabel('host') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->textField($model, 'host', array(
                                'class' => 'form-control jHostCheck', 
                                'required' => 'required', 
                                'placeholder' => ''
                            )); ?>
                        </div>

                        <script type="text/javascript">
                            $(function(){
                                $('.jHostCheck').keyup(function(){
                                    var url = {};a

                                    var v = $(this).val();

                                    var tmp = v.split('//');

                                    if (tmp.length > 1) {
                                        url.scheme = tmp[0];
                                        v = tmp.slice(1).join('//');
                                    }

                                    var tmp = v.split('/');

                                    url.host = tmp[0];
                                    url.data = tmp.slice(1).join('/');

                                    var a = url.host.split('.');

                                    $(this).parent().find('label').remove();

                                    var len = a.length;

                                    if (a[0] == 'www') {
                                    	len--;
                                    }

                                    if (len > 3) {
                                        $(this).parent().append('<label class="validation-error-label">Введен домен более чем третьего уровня, система не может его принять по техническим причинам.</label>');
                                    }
                                    else if (len == 3) {
                                        $(this).parent().append('<label class="validation-error-label">Вами добавлен домен третьего уровня, аудит сайта будет выполнен с учетом основного домена!</label>');
                                    }
                                });
                            });
                        </script>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-4"><?= $model->getAttributeLabel('name') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->textField($model, 'name', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-4"><?= $model->getAttributeLabel('regions') ?></label>
                        <div class="col-lg-8">
                            <?/*= $this->renderPartial('application.views.base.types.multiselect', array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true); */?>

                            <?php echo $form->dropDownList($model, 'regions', Region::getRegionsList(), array('class' => 'select-search')); ?>
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

<script type="text/javascript">
    $(function(){
        $('.select-search').select2();
    });
</script>