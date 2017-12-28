<?php
/**
 * @var CAdminController $this
 * @var CActiveForm $form
 * @var LoginForm $model
 */

$active = 1;

if ($reg_model->hasErrors()) {
    $active = 2;
}

?>

<!-- Tabbed form -->
<div class="tabbable panel login-form width-400">
    <ul class="nav nav-tabs nav-justified">
        <li <?= ($active == 1) ? 'class="active"' : '' ?>><a href="#basic-tab1" data-toggle="tab"><h6>Sign in</h6></a></li>
        <li <?= ($active == 2) ? 'class="active"' : '' ?>><a href="#basic-tab2" data-toggle="tab"><h6>Register</h6></a></li>
    </ul>

    <div class="tab-content panel-body">
        <div class="tab-pane fade <?= ($active == 1) ? 'in active' : '' ?>" id="basic-tab1">

            <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => ''))); ?>
                <div class="text-center">
                    <div class="icon-object border-slate-300 text-slate-300"><i class="icon-reading"></i></div>
                    <h5 class="content-group">Login to your account <small class="display-block">Your credentials</small></h5>
                </div>

                <? if ($model->hasErrors()): ?>
                    <div class="alert alert-error bg-danger-700">
                        <span>Неверное имя пользователя или пароль.</span>
                    </div>

                    <script type="text/javascript">
                        $(function(){
                            $(".alert.alert-error").addClass("animated bounceIn").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                                $(this).removeClass("animated bounceIn");
                            });
                        });
                    </script>
                <? endif; ?>

                <div class="form-group has-feedback has-feedback-left <? if ($model->hasErrors('username')): ?>error<? endif; ?>">
                    <?php echo $form->textField($model, 'username', array('class' => 'form-control', 'required' => 'required', 'placeholder' => $model->getAttributeLabel('username'))); ?>
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left <? if ($model->hasErrors('password')): ?>error<? endif; ?>">
                    <?php echo $form->passwordField($model, 'password', array('class' => 'form-control', 'required' => 'required', 'placeholder' => $model->getAttributeLabel('password'))); ?>
                    <div class="form-control-feedback">
                        <i class="icon-lock2 text-muted"></i>
                    </div>
                </div>

                <div class="form-group login-options">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="checkbox-inline">
                                <?php echo $form->checkBox($model, 'rememberMe', array('class' => 'styled', 'value'=>1, 'uncheckValue'=>0)); ?>
                                <?= $model->getAttributeLabel('rememberMe') ?>
                            </label>
                        </div>

                        <div class="col-sm-6 text-right">
                            <a href="<?= Yii::app()->urlManager->createUrl('site/recovery') ?>">Forgot password?</a>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn bg-blue btn-block">Login <i class="icon-arrow-right14 position-right"></i></button>
                </div>
            <?php $this->endWidget(); ?>

            <span class="help-block text-center no-margin">By continuing, you're confirming that you've read our <a href="#">Terms &amp; Conditions</a> and <a href="#">Cookie Policy</a></span>
        </div>

        <div class="tab-pane fade <?= ($active == 2) ? 'in active' : '' ?>" id="basic-tab2">
            <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => ''))); ?>
                <div class="text-center">
                    <div class="icon-object border-success text-success"><i class="icon-plus3"></i></div>
                    <h5 class="content-group">Create new account <small class="display-block">All fields are required</small></h5>
                </div>

                <? if ($reg_model->hasErrors()): $e = $reg_model->getErrors(); ?>
                    <div class="alert alert-error bg-danger-700">
                        <?= current($e)[0] ?>
                    </div>

                    <script type="text/javascript">
                        $(function(){
                            $(".alert.alert-error").addClass("animated bounceIn").one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                                $(this).removeClass("animated bounceIn");
                            });
                        });
                    </script>
                <? endif; ?>

                <div class="form-group has-feedback has-feedback-left">
                	<?php echo $form->textField($reg_model, 'username', array('class' => 'form-control', 'required' => 'required', 'placeholder' => $reg_model->getAttributeLabel('username'))); ?>
                    <div class="form-control-feedback">
                        <i class="icon-mention text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <?php echo $form->passwordField($reg_model, 'password', array('class' => 'form-control', 'required' => 'required', 'placeholder' => $reg_model->getAttributeLabel('password'))); ?>
                    <div class="form-control-feedback">
                        <i class="icon-user-lock text-muted"></i>
                    </div>
                </div>

                <div class="form-group has-feedback has-feedback-left">
                    <?php echo $form->textField($reg_model, 'name', array('class' => 'form-control', 'required' => 'required', 'placeholder' => $reg_model->getAttributeLabel('name'))); ?>
                    <div class="form-control-feedback">
                        <i class="icon-user text-muted"></i>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox">
                        <label>
                        	<?php echo $form->checkBox($reg_model, 'subscribe', array('class' => 'styled', 'value'=>1, 'uncheckValue'=>0)); ?>
                            Subscribe to monthly newsletter
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                        	<?php echo $form->checkBox($reg_model, 'terms', array('class' => 'styled', 'value'=>1, 'uncheckValue'=>0)); ?>
                            Accept <a href="#">terms of service</a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn bg-indigo-400 btn-block">Зарегистрироваться <i class="icon-circle-right2 position-right"></i></button>
            <?php $this->endWidget(); ?>
        </div>
    </div>
</div>
<!-- /tabbed form -->
