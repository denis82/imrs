<? 
    $this->clientScript->registerScriptFile($this->assetsUrl . '/limitless_1.5/js/pages/form_layouts.js'); 
?>
<div class="panel panel-flat">
    <div class="panel-body form-horizontal">

    <?php $form = $this->beginWidget('CActiveForm', array('htmlOptions' => array('class' => 'form-horizontal jOrgForm', 'enctype' => 'multipart/form-data'))); ?>

        <div class="form-group">
            <label class="control-label col-lg-2">Местоположение</label>
            <div class="col-lg-10">
	            <div class="row mb-10">
	                <div class="col-lg-6">
	                	<?php echo $form->textField($org, 'country', array('class' => 'form-control')); ?>
	                    <span class="help-block"><?= $org->getAttributeLabel('country') ?></span>
	                </div>
	                <div class="col-lg-6">
	                	<?php echo $form->textField($org, 'city', array('class' => 'form-control')); ?>
	                    <span class="help-block"><?= $org->getAttributeLabel('city') ?></span>
	                </div>
	            </div>
	            <div class="row">
	                <div class="col-lg-6">
	                	<?php echo $form->textField($org, 'region', array('class' => 'form-control')); ?>
	                    <span class="help-block"><?= $org->getAttributeLabel('region') ?></span>
	                </div>
	                <div class="col-lg-6">
	                	<?php echo $form->textField($org, 'district', array('class' => 'form-control')); ?>
	                    <span class="help-block"><?= $org->getAttributeLabel('district') ?></span>
	                </div>
	            </div>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('name') ?></label>
            <div class="col-lg-10">
            	<?php echo $form->textField($org, 'name', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('legal') ?></label>
            <div class="col-lg-10">
            	<?php echo $form->textField($org, 'legal', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('english') ?></label>
            <div class="col-lg-10">
            	<?php echo $form->textField($org, 'english', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('address') ?></label>
            <div class="col-lg-10">
            	<?php echo $form->textField($org, 'address', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('phone') ?></label>
            <div class="col-lg-10 jPhone">
        		<?
        			if (!is_array($org->phone_number) or count($org->phone_number) == 0) {
        				$org->phone_number = array(0 => '');
        			}

    				foreach ($org->phone_number as $j => $i) {
    					?>
		            	<div class="phone-line">
		            		<div class="col-lg-6">
		            			<div class="row">
			            			<div class="col-lg-4">
										<div class="input-group">
											<span class="input-group-addon">
												<i class="icon-plus2 small"></i>
											</span>
											<?php echo $form->textField($org, 'phone_country[]', array('class' => 'form-control', 'value' => $org->phone_country[$j])); ?>
										</div>
					                    <span class="help-block">код страны</span>
			            			</div>
			            			<div class="col-lg-3">
										<?php echo $form->textField($org, 'phone_code[]', array('class' => 'form-control', 'value' => $org->phone_code[$j])); ?>
					                    <span class="help-block">код города</span>
			            			</div>
			            			<div class="col-lg-5">
										<?php echo $form->textField($org, 'phone_number[]', array('class' => 'form-control', 'value' => $org->phone_number[$j])); ?>
					                    <span class="help-block">телефон</span>
			            			</div>
			            		</div>
		            		</div>
		            		<div class="col-lg-6">
			            		<div class="col-lg-1" style="padding-top: 8px;">
			            			&mdash;
			            		</div>
			            		<div class="col-lg-9">
			            			<div class="col-lg-6">
										<?php echo $form->textField($org, 'phone_extra[]', array('class' => 'form-control', 'value' => $org->phone_extra[$j])); ?>
					                    <span class="help-block">внутр. номер</span>
			            			</div>
			            			<div class="col-lg-6">
										<?php echo $form->textField($org, 'phone_name[]', array('class' => 'form-control', 'value' => $org->phone_name[$j])); ?>
					                    <span class="help-block">должность, отдел</span>
			            			</div>
			            		</div>
			            		<div class="col-lg-2" style="padding-top: 8px;">
			            			<i class="icon-plus-circle2 jAdd"></i>
			            			<i class="icon-cancel-circle2 jRemove"></i>
			            		</div>
		            		</div>
	            		</div>
    					<?
    				}
        		?> 
            </div>

            <script type="text/javascript">
            $(function(){
            	$('.jPhone').each(function(){
            		var $el = $(this);

	        		$('.phone-line .jAdd', $el).hide();
	        		$('.phone-line').last().find('.jAdd').show();

            		if ($('.phone-line', $el).length < 2) {
	            		$('.phone-line .jRemove', $el).hide();
            		}
            		else {
		        		$('.phone-line .jRemove', $el).show();
            		}

            		$el
		            	.delegate('.jAdd', 'click', function(){
		            		var $input = $(this).closest('.phone-line');

		            		var $new_input = $input.clone();
		            		$('input', $new_input).val('');
		            		$('.jAdd, .jRemove', $new_input).show();

		            		$new_input.appendTo( $el );

		            		$('.jAdd', $input).hide();
		            		$('.jRemove', $input).show();

		            		return false;
		            	})
		            	.delegate('.jRemove', 'click', function(){
		            		$(this).closest('.phone-line').remove();

		            		$('.phone-line .jAdd', $el).hide();
		            		$('.phone-line .jRemove', $el).show();
		            		$('.phone-line', $el).last().find('.jAdd, .jRemove').show();

		            		if ($('.phone-line', $el).length < 2) {
			            		$('.phone-line .jRemove', $el).hide();
		            		}

		            		return false;
		            	})
		            ;

            	});
            });
            </script>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('site') ?></label>
            <div class="col-lg-10 jSite">
        		<?
        			if (!is_array($org->site) or count($org->site) == 0) {
        				$org->site = array(0 => '');
        			}

    				foreach ($org->site as $j => $i) {
    					?>
		            	<div class="row mb-10 input-website">
		            		<div class="col-lg-10">
				            	<?php echo $form->textField($org, 'site[]', array('class' => 'form-control', 'value' => $i)); ?>
		            		</div>
		            		<div class="col-lg-2" style="padding-top: 8px;">
		            			<i class="icon-plus-circle2 jAdd"></i>
		            			<i class="icon-cancel-circle2 jRemove"></i>
		            		</div>
		            	</div>
    					<?
					}
				?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('social') ?></label>
            <div class="col-lg-10 jSite">
        		<?
        			if (!is_array($org->social) or count($org->social) == 0) {
        				$org->social = array(0 => '');
        			}

    				foreach ($org->social as $j => $i) {
    					?>
		            	<div class="row mb-10 input-website">
		            		<div class="col-lg-10">
				            	<?php echo $form->textField($org, 'social[]', array('class' => 'form-control', 'value' => $i)); ?>
		            		</div>
		            		<div class="col-lg-2" style="padding-top: 8px;">
		            			<i class="icon-plus-circle2 jAdd"></i>
		            			<i class="icon-cancel-circle2 jRemove"></i>
		            		</div>
		            	</div>
    					<?
					}
				?>
            </div>

            <script type="text/javascript">
            $(function(){
            	$('.jSite').each(function(){
            		var $el = $(this);

	        		$('.input-website .jAdd', $el).hide();
	        		$('.input-website').last().find('.jAdd').show();

            		if ($('.input-website', $el).length < 2) {
		        		$('.input-website .jAdd', $el).show();
	            		$('.input-website .jRemove', $el).hide();
            		}
            		else {
		        		$('.input-website .jRemove', $el).show();
            		}

            		$el
		            	.delegate('.jAdd', 'click', function(){
		            		var $input = $(this).closest('.input-website');

		            		var $new_input = $input.clone();
		            		$('input', $new_input).val('');
		            		$('.jAdd, .jRemove', $new_input).show();

		            		$new_input.appendTo( $el );

		            		$('.jAdd', $input).hide();
		            		$('.jRemove', $input).show();

		            		return false;
		            	})
		            	.delegate('.jRemove', 'click', function(){
		            		$(this).closest('.input-website').remove();

		            		$('.input-website .jAdd', $el).hide();
		            		$('.input-website .jRemove', $el).show();
		            		$('.input-website', $el).last().find('.jAdd, .jRemove').show();

		            		if ($('.input-website', $el).length < 2) {
			            		$('.input-website .jRemove', $el).hide();
		            		}

		            		return false;
		            	})
		            ;

            	});
            });
            </script>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('email') ?></label>
            <div class="col-lg-10">
                <?php echo $form->textField($org, 'email', array('class' => 'form-control')); ?>
            </div>
        </div>                    

        <div class="form-group">
            <label class="control-label col-lg-2"><?= $org->getAttributeLabel('worktime') ?></label>
            <div class="col-lg-10 jWorktime">
        		<?
        			if (!is_array($org->worktime_days) or count($org->worktime_days) == 0) {
        				$org->worktime_days = array(0 => '');
        			}

    				foreach ($org->worktime_days as $j => $i) {
    					?>

		            	<div class="row mb-10 input-worktime">
							<?php echo $form->hiddenField($org, 'worktime_days[]', array('class' => 'form-control', 'value' => $org->worktime_days[$j])); ?>

		            		<div class="col-lg-5">
				            	<button class="btn btn-default jDoW" data-name="1">пн</button>
				            	<button class="btn btn-default jDoW" data-name="2">вт</button>
				            	<button class="btn btn-default jDoW" data-name="3">ср</button>
				            	<button class="btn btn-default jDoW" data-name="4">чт</button>
				            	<button class="btn btn-default jDoW" data-name="5">пт</button>
				            	<button class="btn btn-default jDoW" data-name="6">сб</button>
				            	<button class="btn btn-default jDoW" data-name="7">вс</button>
		            		</div>

		            		<div class="col-lg-5">
		            			<div class="row">
		            				<div class="col-lg-5">
										<?php echo $form->timeField($org, 'worktime_time1[]', array('class' => 'form-control', 'value' => $org->worktime_time1[$j])); ?>
		            				</div>
		            				<div class="col-lg-2 text-center" style="padding-top: 8px;">
		            					&mdash;
		            				</div>
		            				<div class="col-lg-5">
										<?php echo $form->timeField($org, 'worktime_time2[]', array('class' => 'form-control', 'value' => $org->worktime_time2[$j])); ?>
		            				</div>
		            			</div>
		            		</div>

		            		<div class="col-lg-2" style="padding-top: 8px;">
		            			<i class="icon-plus-circle2 jAdd"></i>
		            			<i class="icon-cancel-circle2 jRemove"></i>
		            		</div>
		            	</div>

		            	<?
		            }
		        ?>
            </div>

            <script type="text/javascript">
            $(function(){
            	$('.jWorktime').each(function(){
            		var $el = $(this);

	        		$('.input-worktime .jAdd', $el).hide();
	        		$('.input-worktime').last().find('.jAdd').show();

            		if ($('.input-worktime', $el).length < 2) {
		        		$('.input-worktime .jAdd', $el).show();
	            		$('.input-worktime .jRemove', $el).hide();
            		}
            		else {
		        		$('.input-worktime .jRemove', $el).show();
            		}
            	});

            	$('.jWorktime .input-worktime').each(function(){
            		if ($('input[type=hidden]', this).val().length) {
            			var v = $('input[type=hidden]', this).val().split(',');

            			$('.jDoW', this).each(function(){
            				var i = $(this).data('name') + "";

            				if ($.inArray( i, v ) >= 0) {
            					$(this).addClass('btn-primary');
            				}
            			});
            		}
            	});

            	$('.jWorktime')
	            	.delegate('.jDoW', 'click', function(){
	            		var name = $(this).data('name');

	            		if ($(this).hasClass('btn-primary')) {
	            			$(this).removeClass('btn-primary')
	            		}
	            		else {
		            		$('.jDoW[data-name=' + name + ']').removeClass('btn-primary');
	            			$(this).addClass('btn-primary')
	            		}

	            		return false;
	            	})
	            	.delegate('.jAdd', 'click', function(){
	            		var $el = $(this).closest('.jWorktime');
	            		var $input = $(this).closest('.input-worktime');

	            		var $new_input = $input.clone();
	            		$('.jDoW', $new_input).removeClass('btn-primary');

	            		$new_input.appendTo( $el );

	            		$('.jAdd', $input).hide();
	            		$('.jRemove', $input).show();

	            		$('.input-worktime', $el).last().find('.jAdd, .jRemove').show();

	            		return false;
	            	})
	            	.delegate('.jRemove', 'click', function(){
	            		var $el = $(this).closest('.jWorktime');
	            		
	            		$(this).closest('.input-worktime').remove();

	            		$('.input-worktime .jAdd', $el).hide();
	            		$('.input-worktime .jRemove', $el).show();
	            		$('.input-worktime', $el).last().find('.jAdd, .jRemove').show();

	            		if ($('.input-worktime', $el).length < 2) {
		            		$('.input-worktime .jRemove', $el).hide();
	            		}

	            		return false;
	            	})
	            ;
            });
            </script>
        </div>   

        <div class="row">
        	<div class="col-lg-6 col-md-6 text-left">
        		<button type="button" class="btn btn-danger"><i class="icon-arrow-left8"></i> Отмена</button>
        	</div>
        	<div class="col-lg-6 col-md-6 text-right">
        		<button type="submit" class="btn btn-success"><i class="icon-checkmark4"></i> Сохранить</button>
        	</div>
        </div>                 

    <?php $this->endWidget(); ?>
    </div>
</div>

<script type="text/javascript">
$(function(){

	$('.btn-success').click(function(){
		$('.jWorktime .input-worktime').each(function(){
			var s = [];

			$('.jDoW.btn-primary', this).each(function(){
				s.push( $(this).data('name') );
			});

			$('input[type=hidden]', this).first().val( s.join(',') );
		});
	});

});
</script>
