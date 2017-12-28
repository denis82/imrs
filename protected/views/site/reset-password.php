<?php
/**
 * @var CAdminController $this
 * @var CActiveForm $form
 * @var LoginForm $model
 */

?>

<!-- Password recovery -->
    <div class="panel panel-body login-form">
        <div class="text-center">
            <div class="icon-object border-warning text-warning"><i class="icon-spinner11"></i></div>
            <h5 class="content-group">
                Восстановление пароля 
                <small class="display-block">Введите ваш новый пароль</small>
            </h5>
        </div>

        <? if (!$model): ?>

            <div class="alert alert-danger alert-styled-left alert-bordered">
                Ваша ссылка не действительна. Откройте ссылку отправленную вам на e-mail ещё раз, или пройдите <a href="<?= Yii::app()->urlManager->createUrl('site/recovery') ?>">процедуру восстановления пароля ещё раз</a>.
            </div>

        <? else: ?>

            <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => ''))); ?>

                <? if ($model->hasErrors()): ?>
                    <div class="alert alert-error bg-danger-700">
                        <span><?= $model->getError() ?></span>
                    </div>

                    <script type="text/javascript">
                        $(function(){
                            $(".alert.alert-error").addClass("animated bounceIn").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                                $(this).removeClass("animated bounceIn");
                            });
                        });
                    </script>
                <? endif; ?>

                <div class="form-group has-feedback  <? if ($model->hasErrors('password')): ?>error<? endif; ?>">

                    <?php echo $form->passwordField($model, 'password', 
                        array('class' => 'form-control', 'required' => 'required', 'placeholder' => $model->getAttributeLabel('password'))); ?>

                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>

                </div>

                <div class="form-group has-feedback  <? if ($model->hasErrors('password_confirm')): ?>error<? endif; ?>">

                    <?php echo $form->passwordField($model, 'password_confirm', 
                        array('class' => 'form-control', 'required' => 'required', 'placeholder' => $model->getAttributeLabel('password_confirm'))); ?>

                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>

                </div>

                <button type="submit" class="btn bg-blue btn-block">Reset password <i class="icon-arrow-right14 position-right"></i></button>

            <?php $this->endWidget(); ?>

        <? endif; ?>
    </div>

<!-- /password recovery -->

