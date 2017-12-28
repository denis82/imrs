<script type='text/javascript'>
	$(function(){
		var $keywordsPrice = $('#keywords-price'),
			$applyButton = $('#apply-button'),
			$loaderImage = $('#loader-image'),
			__actionUpdate = false;
		$applyButton.click(function(){
			if(__actionUpdate) return false;
			__actionUpdate = true;
			$loaderImage.show();
			$applyButton.addClass('black').removeClass('green');
			$.post('/seo/project/updateprice/<?=$project->id;?>',$keywordsPrice.serialize(),function(data){
				if(data.success){
				}else{
					alert(data.error);
				}
				$applyButton.addClass('green').removeClass('black');
				$loaderImage.hide();
				__actionUpdate = false;
			},'json');
		});
	});
</script>

<div class="row-fluid">
	<div class="span12">
		<form method="get" id="region-change-form">
			<div class="dataTables_filter">
				<label class="filters">Регион:  <?php echo CHtml::dropDownList('region', $region, $regions, array('class' => 'm-wrap medium', 'onchange' => '$("#region-change-form").submit();')); ?></label>
			</div>
		</form>
	</div>
</div>

<form method='post' id='keywords-price'>

<input type='hidden' name='region' value='<?=$region;?>' />
<input type='hidden' name='<?=Yii::app()->request->csrfTokenName;?>' value='<?=Yii::app()->request->csrfToken;?>' />

<div class="row-fluid">
	<div class="span12">
		<table class="table table-striped table-bordered table-hover">
			<thead>
			<tr>
				<th>Ключевая фраза</th>
				<th style='width:250px'>Цена</th>
				<th style='width:150px'></th>
			</tr>
			</thead>
			<? foreach ($keywords as $obj): ?>
				<tr>
					<td>
						<?=$obj->keyword->keyword;?>
					</td>
					<td>
						<?//=$price[$obj->keyword_id]->price;?>
						
						<div class="input-append">
							<input type='text' name='price[<?=$obj->keyword_id;?>]' value='<?=(($price[$obj->keyword_id]==NULL)?0:$price[$obj->keyword_id]->price)?>' />
							<span class="add-on">РУБ.</span>
						</div>
						
					</td>
					<td>
						<a class='btn red' href='javascript:void(0);'><i class='icon-remove'></i> Удалить</a>
					</td>
				</tr>
			<? endforeach; ?>
			<tr>
				<td></td>
				<td>
					<a id='apply-button' class='btn green' href='javascript:void(0);'><i class='icon-ok'></i> Применить<a>
					<img id='loader-image' src='/images/ajax-loader.gif' width='31' height='31' alt='' style='display:none;' />
				</td>
				<td></td>
			</tr>
		</table>
	</div>
</div>
</form>