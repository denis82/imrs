<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>
<div class="row">
    <div class="col-lg-6">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Профиль</h5>
            </div>

            <div class="panel-body">
                <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'))); ?>

                    <div class="form-group">
                        <label class="control-label col-lg-3"><?= $profile_form->getAttributeLabel('name') ?></label>
                        <div class="col-lg-9">
                            <?php echo $form->textField($profile_form, 'name', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="col-lg-3 control-label"><?= $profile_form->getAttributeLabel('avatar') ?></label>
                        <div class="col-lg-9">
                            <div class="media no-margin-top">
                                <div class="media-left">
                                    <a href="#"><img src="<?= $model->photo ? CImageHelper::crop($model->photo, 120, 120) : User::defaultAvatar ?>" width="60px" height="60px" class="img-circle" alt=""></a>
                                </div>

                                <div class="media-body">
                                    <?php echo $form->fileField($profile_form, 'avatar', array('class' => 'file-styled')); ?>
                                    <span class="help-block">Форматы файлов: gif, png, jpg. Не более 2Mb</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Сохранить <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                <?php $this->endWidget(); ?>

            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h5 class="panel-title">Авторизация</h5>
            </div>

            <div class="panel-body">
                <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal', 'enctype' => 'multipart/form-data'))); ?>

                    <? if ($password_form->hasErrors()): ?>
                        <div class="alert alert-error bg-danger-700">
                            <?= current($password_form->getErrors())[0] ?>
                        </div>
                    <? elseif ($password_form->saved): ?>
                        <div class="alert alert-error bg-success-700">
                            Пароль изменен.
                        </div>
                    <? endif; ?>

                    <div class="form-group <?= $model->email_confirmed ? 'has-success' : 'has-warning' ?> has-feedback">
                        <label class="control-label col-lg-4"><?= $password_form->getAttributeLabel('email') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->textField($password_form, 'email', array(
                                'class' => 'form-control', 
                                'disabled' => 'disabled',
                                'title' => $model->email_confirmed ? 'Подтвержден' : 'Не подтвержден',
                            )); ?>

                            <? if ($model->email_confirmed): ?>
                                <div class="form-control-feedback">
                                    <i class="icon-checkmark-circle"></i>
                                </div>
                            <? else: ?>
                                <div class="form-control-feedback">
                                    <i class="icon-notification2"></i>
                                </div>
                                <span class="help-block">Ещё не подтвержден</span>
                            <? endif; ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-4"><?= $password_form->getAttributeLabel('old_password') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->passwordField($password_form, 'old_password', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-4"><?= $password_form->getAttributeLabel('new1_password') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->passwordField($password_form, 'new1_password', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="form-group">
                        <label class="control-label col-lg-4"><?= $password_form->getAttributeLabel('new2_password') ?></label>
                        <div class="col-lg-8">
                            <?php echo $form->passwordField($password_form, 'new2_password', array('class' => 'form-control', 'required' => 'required')); ?>
                        </div>
                    </div>                    

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Поменять пароль <i class="icon-arrow-right14 position-right"></i></button>
                    </div>
                <?php $this->endWidget(); ?>
            </div>
        </div>
    </div>
</div>
