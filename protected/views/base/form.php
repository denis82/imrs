<div class="panel panel-flat">
    <div class="panel-body">
        <div class="portlet-body">            
            <div class="row-fluid">
                <div class="span12">
                    <div class="clearfix">
                        <!-- BEGIN FORM-->                        
                        <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'))); ?>
						<??>
						<? if(isset($errors) && count($errors)): ?>
							<div class="alert alert-block alert-error fade in">
                                <button type="button" class="close" data-dismiss="alert"></button>
                                <p>
                                    <? foreach($errors as $error): ?>                                    
                                        <?= $error ?><br/>
                                    <? endforeach; ?>
                                </p>
                            </div>
						<? endif; ?>
						<? if (count($model->errors) > 0): ?>
                            <div class="alert alert-block alert-error fade in">
                                <button type="button" class="close" data-dismiss="alert"></button>
                                <h4 class="alert-heading">Ошибка!</h4>
                                <p>
                                    <? foreach ($model->errors as $error): ?>                                    
                                        <?= $error[0] ?><br/>
                                    <? endforeach; ?>
                                </p>                           
                            </div>
                        <? endif; ?>
                        <? if (isset($formElements["tabs"])): ?>
                            <div class="tabbable tabbable-custom tabbable-full-width">
                                <ul class="nav nav-tabs">
                                    <? foreach ($formElements["tabs"] as $tabId => $tab): ?>
                                        <li <? if ($tabId == 0): ?>class="active"<? endif; ?>><a href="#tab_<?= $tabId ?>" data-toggle="tab"><?= $tab["title"] ?></a></li>
                                    <? endforeach; ?>                                    
                                </ul>
                                <div class="tab-content">
                                    <? foreach ($formElements["tabs"] as $tabId => $tab): ?>                                    
                                        <div class="tab-pane row-fluid <? if ($tabId == 0): ?>active<? endif; ?>" id="tab_<?= $tabId ?>">
                                            <? foreach ($tab['rows'] as $rowId => $row): ?>                                                
                                                <? if (is_string($row)): ?>
                                                    <h3 class="form-section"><?= $row ?></h3>                                                    
                                                <? endif; ?>
                                                <? if (is_array($row)): ?>
                                                    <div class="row-fluid">
                                                        <? foreach ($row as $field => $element): ?>                                                        
                                                            <div class="span<?= $element["grid"] ? $element["grid"] : 12 ?>">
                                                                <? if (!isset($element['htmlOptions'])) $element['htmlOptions'] = array(); ?>
                                                                <? if ($this->getViewFile($this->module->id . ".views.edit." . $element['type'])): ?>
                                                                    <?= $this->renderPartial($this->module->id . ".views." . $this->id . ".types." . $element['type'], array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true) ?>
                                                                <? else: ?>
                                                                    <?= $this->renderPartial('application.views.base.types.' . $element['type'], array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true); ?>
                                                                <? endif; ?>                                            
                                                            </div>
                                                            <!--/span-->
                                                        <? endforeach; ?>
                                                    </div>
                                                    <!--/row-->
                                                <? endif; ?>
                                            <? endforeach; ?>
                                        </div>  
                                    <? endforeach; ?>
                                </div>
                            </div>
                        <? endif; ?>
                        <? if (isset($formElements["rows"])): ?>
                            <? foreach ($formElements['rows'] as $rowId => $row): ?>                                                
                                <? if (is_string($row)): ?>
                                    <h3 class="form-section"><?= $row ?></h3>                                                    
                                <? endif; ?>
                                <? if (is_array($row)): ?>
                                    <div class="row-fluid">
                                        <? foreach ($row as $field => $element): ?>                                                        
                                            <div class="span<?= $element["grid"] ? $element["grid"] : 12 ?>">
                                                <? if (!isset($element['htmlOptions'])) $element['htmlOptions'] = array(); ?>
                                                <? if ($this->getViewFile($this->module->id . ".views.edit." . $element['type'])): ?>
                                                    <?= $this->renderPartial($this->module->id . ".views." . $this->id . ".types." . $element['type'], array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true) ?>
                                                <? else: ?>
                                                    <?= $this->renderPartial('application.views.base.types.' . $element['type'], array('form' => $form, 'model' => $model, 'field' => $field, 'attribute' => $element), true); ?>
                                                <? endif; ?>                                            
                                            </div>
                                            <!--/span-->
                                        <? endforeach; ?>
                                    </div>
                                    <!--/row-->
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                        <div class="form-actions">
                            <button type="submit" class="btn blue" name="submit"><i class="icon-ok"></i> Сохранить</button>
                            <button type="submit" class="btn green" name="apply"><i class="icon-ok"></i> Применить</button>
                            <a href="<?= Yii::app()->urlManager->createUrl($this->module->id . '/' . $this->id . '/index') ?>" class="btn red"><i class="icon-arrow-left"></i> Отмена</a>
                        </div>
                        <?php $this->endWidget(); ?>
                        <!-- END FORM-->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>