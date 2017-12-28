<div class="control-group">
    <?php echo $form->labelEx($model, $field, array('class' => 'control-label')); ?>
    <div class="controls">
		<? $attribute['htmlOptions']['class'] .= 'input-xxlarge'; ?>
		<? //$attribute['htmlOptions']['multiple'] .= 'multiple'; ?>
		<? $attribute['htmlOptions']['type'] = 'hidden'; ?>
		<?php //echo $form->dropDownList($model, $field, $attribute['items'], $attribute['htmlOptions']); ?>
		<div class="input-append">
			<?php echo $form->textField($model, $field, $attribute['htmlOptions']); ?>
			 <button id='keyrows_add' class="btn" type="button">Добавить</button>
		</div>
		<div style='clear:both;'></div>
		<p>Ключевые фразы не могут превышать 255 символов. <br /><span style='color:red;'>Перечисление ключевых фраз через запятую!</span></p>
		<table id='keywords_table' class="table table-condensed table-striped table-bordered">
			<thead>
				<tr>
					<th>Ключевая фраза</th>
					<th width='100'>Удалить</th>
				</tr>
			</thead>
			<tbody>
			</tbody>
		</table>
		
		<div style='clear:both;'></div>
    </div>
</div>
<script type='text/javascript'>
	$(function(){
		var csrfToken = '<?=Yii::app()->request->csrfToken;?>',
			keywords_array = new Array(),
			keywords_table = $('#keywords_table'),
			keywords_tbody = keywords_table.find('tbody');
		var pre_field_name = '<?=get_class($model)?>';
		var keywords_add_processing = window.keywords_add_processing = 0;
		var keywords_add_textfield = window.keywords_add_textfield = $('#<?=(get_class($model).'_'.$field);?>')[0];
		$('#keyrows_add').bind('click',function(){
			if(window.keywords_add_processing) return false;
			var __val =  window.keywords_add_textfield.value;
			if( __val.length < 3 ) return alert('Ключевая фраза не может быть менее 3 символов.');
			window.keywords_add_processing = 1;
			$.post('<?=$attribute['link'];?>',{value:__val,YII_CSRF_TOKEN:csrfToken},function(data){
				if(data.success){
					$.each(data.elements, function(index, value) {
						if( keywords_array[index] ) return;
						keywords_array[index] = value;
						var __tr_string = '';
						__tr_string += '<tr>';
						__tr_string += '<td>'+value+'</td>';
						__tr_string += '<td><button class="btn btn-mini red" type="button">Удалить</button><input type="hidden" name="keywords[]" value="'+index+'"></td>';
						__tr_string += '</tr>';
						var __tr = $(__tr_string);
						__tr.find('button').click(function(){
							__tr.remove();
						});
						keywords_tbody.append(__tr);
					});
					//alert('gut');
				}else alert(data.error);
				window.keywords_add_processing = 0;
			},'json');
		});
	});
</script>