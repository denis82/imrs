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
            <h5 class="content-group">Password recovery <small class="display-block">We'll send you instructions in email</small></h5>
        </div>

        <? if ($result): ?>

        <div class="alert alert-success alert-styled-left alert-arrow-left alert-bordered">
            На ваш e-mail отправлены инструкции по восстановлению пароля.
        </div>

        <? else: ?>

            <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => ''))); ?>

                <? if ($model->hasErrors()): ?>
                    <div class="alert alert-error bg-danger-700">
                        <span>Пользователя с таким e-mail не существует.</span>
                    </div>

                    <script type="text/javascript">
                        $(function(){
                            $(".alert.alert-error").addClass("animated bounceIn").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                                $(this).removeClass("animated bounceIn");
                            });
                        });
                    </script>
                <? endif; ?>

                <div class="form-group has-feedback  <? if ($model->hasErrors('username')): ?>error<? endif; ?>">

                    <?php echo $form->textField($model, 'username', 
                        array('class' => 'form-control', 'required' => 'required', 'placeholder' => $model->getAttributeLabel('username'))); ?>

                    <div class="form-control-feedback">
                        <i class="icon-mail5 text-muted"></i>
                    </div>

                </div>

                <button type="submit" class="btn bg-blue btn-block">Reset password <i class="icon-arrow-right14 position-right"></i></button>

            <?php $this->endWidget(); ?>
        <? endif; ?>
    </div>

<!-- /password recovery -->

