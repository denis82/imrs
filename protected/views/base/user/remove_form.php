
<div class="panel panel-flat">
    <div class="panel-heading">

        <h5 class="panel-title text-semiold">Удаление аккаунта</h5>

    </div>
    <div class="panel-body">

		<div class="alert alert-danger alert-styled-left alert-bordered">
			<span class="text-semibold">Внимание!</span> 
			Вы действительно хотите удалить свой аккаунт?
		</div>    

        <?php $form = $this->beginWidget('CActiveForm'); ?>

            <?php echo $form->hiddenField($model, 'email'); ?>

			<div class="row">
				<div class="col-lg-6">
					<div class="text-left">
						<a href="<?= Yii::app()->createUrl('main/user/profile') ?>" class="btn btn-primary"><i class="icon-arrow-left13 position-left"></i> Отмена</a>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="text-right">
						<button type="submit" class="btn btn-danger"><i class="icon-trash-alt position-left"></i> Удалить</button>
					</div>
				</div>
			</div>

		<?php $this->endWidget(); ?>

    </div>
</div>



