<script type='text/javascript'>
	$(function(){
		//function
	});
	
	var remove_keyword = function(link,auditkeyword_id){
		$.getJSON('/seo/audit/removekeyword/'+auditkeyword_id,{},function(data){
			if(data.success){
				var _tr = $(link).parents().filter('tr').first();
				//_tr.remove();
				_tr.css({opacity:0.5});
				$(link).remove();
			}else{
				alert(data.error);
			}
		});
	};
	
	window.remove_keyword = remove_keyword;
	
	var Audit = {
		links:{
			keywords:'#link_auditKeywords',
			positions:'#link_auditPositions',
			create:'#link_auditCreate',
		},
		keywords:{
			exists:false,
			remove:function(link,auditkeyword_id){
				$.getJSON('/seo/audit/removekeyword/'+auditkeyword_id,{},function(data){
					if(data.success){
						var _tr = $(link).parents().filter('tr').first();
						_tr.css({opacity:0.5});
						$(link).remove();
					}else{
						alert(data.error);
					}
				});
			},
			complete:function(){
				Audit.keywords.exists = true;
				$(Audit.links.keywords).removeClass('red').addClass('green');
			},
		},
		positions:{
			exists:false,
			complete:function(){
				Audit.positions.exists = true;
				$(Audit.links.positions).removeClass('red').addClass('green');
				Audit.check();
			},
		},
		check:function(){
			if(Audit.keywords.exists && Audit.positions.exists){
				$(Audit.links.create).show();
			}
		}
	};
	<?if($keywords):?>
	Audit.keywords.exists = true;
	<?endif;?>
	<?if($positions):?>
	Audit.positions.exists = true;
	<?endif;?>
	
	$(function(){
		Audit.check();
	});
</script>



<div class="row-fluid">
<div class="span12">
<div class="portlet-body">
<div class="row-fluid">
<div class="span12">
<div class="clearfix">

<?php
$intreval = date_diff(date_create($siteInfo->created), date_create(date('Y-m-d')));
function f_no($string){ return '<span style="color:red;">'.$string.'</span>';}
function f_yes($string){ return '<span style="color:green;">'.$string.'</span>';}
$hosterName = false;
if( $siteInfo->hoster != '' ){
	$hoster_array = array_reverse(explode('.',$siteInfo->hoster));
	$hosterName = $hoster_array[1] . '.' . $hoster_array[0];
	if(is_numeric($hoster_array[0])) $hosterName = false;
}
?>

<div class="tabbable tabbable-custom tabbable-full-width">
<!--ul class="nav nav-tabs">
    <li class="active"><a href="#tab_1" data-toggle="tab">Текущая статистика</a></li>
	<li><a href="#tab_3" data-toggle="tab">Ключевые фразы</a></li>
	<li><a href="#tab_4" data-toggle="tab">Определение позиций</a></li>
    <li><a href="#tab_2" data-toggle="tab">История аудитов</a></li>
</ul-->

<!--h1 style="color:red;font-size:30px;">ИДУТ ТЕХНИЧЕСКИЕ РАБОТЫ</h1-->

<div class='controls'>
	<div class="btn-group">
		<a class='btn' href='#tab_1' data-toggle='tab'><i class='icon-check'></i> <span>Актуальная информация</span></a>
		<a class='btn' href='#tab_2' data-toggle='tab'><i class='icon-tasks'></i> <span>История аудитов</span></a>
		
	</div>
	<div class="btn-group">
		<a id='link_auditKeywords' class='btn <?if($keywords):?>green<?else:?>red<?endif;?>' href='#tab_3' data-toggle="tab"><i class='icon-tags'></i> <span>1. Ключевые фразы</span></a>
		<a id='link_auditPositions' class='btn <?if($positions):?>green<?else:?>red<?endif;?>' href='#tab_4' data-toggle="tab"><i class='icon-list-alt'></i> <span>2. Определение позиций</span></a>
		<a id='link_auditCreate' class='btn green' href='/seo/audit/audit/<?=$audit->id;?>' <?if(!$positions || !$keywords):?>style='display:;'<?endif;?>><i class='icon-arrow-down'></i> <span>3. Создать аудит</span></a>
	</div>
	
</div>

<div class="tab-content">
<div class="tab-pane row-fluid" id="tab_1">

<h3>1. Базовая информация и Анализ настроек сервера под поисковые системы</h3>
<ol>
	<li>Дата регистрация домена: <strong><?=$siteInfo->created;?></strong> (Возраст: <?=$intreval->format('%Y г., %M мес., %d дн.');?>)</li>
	<li>Хостинг сайта: <strong><?=$siteInfo->hoster;?> <?=(( $hosterName )? f_yes('('.$hosterName.')') : '');?></strong></li>
	<li>Предположение о системе управления сайтом (CMS): <strong><?=($siteInfo->cms=='Не определено')?f_no('не определено'):f_yes($siteInfo->cms);?></strong></li>
	<li>Файл инструкций для поисковых систем robots.txt: <strong><?=($siteInfo->robots)?f_yes('есть'):f_no('нет');?></strong></li>
	<li>Карта сайта (sitemap.xml): <strong><?=($siteInfo->sitemap)?f_yes('есть'):f_no('нет');?></strong></li>
	<li>Обработка ошибки 404: <strong><?=($siteInfo->error404)?f_yes('есть'):f_no('нет');?></strong></li>
	<li>Ответ сервера на запрос даты последней модификации документа: <strong><?=($siteInfo->last_modified==0)?f_no('нет'):date('d.m.Y', $siteInfo->last_modified);?></strong></li>
</ol>

<h3>2. Информация о настройках главной страницы сайта</h3>
<ol>
	<li>Заголовок главной страницы (Title): <strong><?=$siteInfo->title;?></strong></li>
	<li>Мета-тег description: <strong><?=$siteInfo->description;?></strong></li>
	<li>Мета-тег keywords: <strong><?=$siteInfo->keywords;?></strong></li>
	<li>Заголовки текста H1-H6  на главной странице: <strong><?=($siteInfo->h1h6)?f_yes('есть'):f_no('нет');?></strong></li>
	<li>Атрибуты alt и title иллюстраций на главной странице: <strong><?=($siteInfo->alts)?f_yes('есть'):f_no('нет');?></strong></li>
</ol>
<h3>3. Формальные признаки сайта и системы статистики и аналитики</h3>
<ol>
	<li>ТИЦ: <strong><?=$siteInfo->tic;?></strong></li>
	<li>PageRank (главной): <strong><?=($siteInfo->pr==NULL)?f_no('не присвоен'):$siteInfo->pr;?></strong></li>
	<li>Яндекс-каталог: <strong><?=($siteInfo->yac)?f_yes('зарегистрирован'):f_no('не зарегистрирован');?></strong></li>
	<li>Яндекс-метрика: <strong><?=($siteInfo->yam)?f_yes('код метрики найден'):f_no('код метрики не найден');?></strong></li>
	<li>Яндекс-вебмастер: <strong><?=($siteInfo->yaw)?f_yes('код подтверждения найден'):f_no('код подтверждения не найден');?></strong></li>
	<li>Google-analytics: <strong><?=($siteInfo->ga)?f_yes('код аналитики найден'):f_no('код аналитики не найден');?></strong></li>
	<li>Google-webmaster: <strong><?=($siteInfo->gw)?f_yes('код подтверждения найден'):f_no('код подтверждения не найден');?></strong></li>
	<li>Вероятность того, что сайт продвигался ранее: <strong><?=($siteInfo->optimized > 0)?f_yes($siteInfo->optimized.'%'):f_no('сайт не продвигали');?></strong></li>
</ol>

<h3>4. Информация об индексации сайта, входящих и исходящих ссылках</h3>
<ol>
	<li>Количество проиндексированных страниц: <strong><?= $siteInfo->index_count; ?></strong></li>
	<li>Кол-во зеркал домена: <strong><?= $siteInfo->mr_sites; ?></strong></li>
	<li>Кол-во доменов на том же ip: <strong><?= $siteInfo->ip_sites; ?></strong></li>
	<li>Количество ссылок на домен: <strong><?= $siteInfo->hin; ?></strong></li>
	<li>Кол-во доноров: <strong><?= $siteInfo->din; ?></strong></li>
	<li>Кол-во найденных анкоров: <strong><?= $siteInfo->anchors; ?></strong></li>
	<li>Исходящие ссылки домена: <strong><?= $siteInfo->hout; ?></strong></li>
	<li>Кол-во доменов, на которые ссылается данный хост: <strong><?= $siteInfo->dout; ?></strong></li>
	<li>Кол-во исходящих анкоров: <strong><?= $siteInfo->anchors_out; ?></strong></li>
</ol>
<h3>5. Предположение о продвигаемых ключевых словах</h3>

<div id='anchor_cloud'>Загрузка, подождите пожалуйста...</div>
<script type='text/javascript'>
	$(function(){
		var _anchor_cloud_block = $('#anchor_cloud');
		var _audit_id = '<?=$audit->id;?>';
		$.getJSON('/seo/audit/anchors/'+_audit_id,{},function(data){
			_anchor_cloud_block.empty();
			if(!data.anchorCloudStatData)
				$('<span style="color:red;">Не удалось получить данные Ahrefs.</span>').appendTo(_anchor_cloud_block);
			else
				$.each(data.anchorCloudStatData, function(i,value){
					$('<span>'+value.anchor+' ('+Math.round(value.percent)+'%) </span>').appendTo(_anchor_cloud_block);
				});
		});
	});
</script>

<h3>6. Текущие позиции сайта в поисковых системах</h3>

<?if( $positions ):?>
<table class="table table-striped table-bordered table-hover">
	<thead>
		<tr>
			<th>Запрос</th>
			<th>Yandex</th>
		</tr>
	</thead>
	<tbody>
	<?foreach($positions as $position):?>
		<tr>
			<td><?=$position->keyword;?></td>
			<td><?=($position->position>100 || $position->position==0)?f_no('НЕТ в топ100'):$position->position;?></td>
		</tr>
	<?endforeach;?>
	</tbody>
</table>
<?else:?>
Позиций, на текущий день, не найдено.
<?endif;?>

</div>
<div class="tab-pane row-fluid" id="tab_2">
	<?if($history && count($history)):?>
		<table class='table table-condensed table-striped table-bordered'>
		<?foreach($history as $auditHistory):?>
			<tr>
				<td><?=$auditHistory->date;?></td>
				<td><a href='/seo/audit/audit/<?=$auditHistory->audit_id;?>?date=<?=$auditHistory->date;?>'>скачать</a></td>
			</tr>
		<?endforeach;?>
		</table>
	<?else:?>
		<p>Аудитов сгенерировано и скачано еще не было.</p>
	<?endif;?>
</div>

<div class="tab-pane row-fluid" id="tab_3">
	<?if($keywords && count($keywords)):?>
		<table class='table table-condensed table-striped table-bordered'>
			<thead>
				<tr>
					<th>Ключевая фраза</th>
					<th>Удаление</th>
				</tr>
			</thead>
			<tbody>
			<?foreach($keywords as $keyword):?>
				<tr>
					<td><?=$keyword->keyword->keyword;?></td>
					<td><a class='btn btn-mini red' onclick="Audit.keywords.remove(this,<?=$keyword->id;?>);return false;">удалить</a></td>
				</tr>
			<?endforeach;?>
			</tbody>
		</table>
	<?else:?>
		<p>Ключевых слов, связанных с этим мини-аудитом, не найдено.</p>
	<?endif;?>
</div>

<script type='text/javascript'>
	var set_label = function(label){
		$('#progress-container').find('.control-label').html(label);
	};
	var add_error = function(string){
		$('#progress-errors').append($('<div></div>').html(string));
	};
		
	var started_scan = 0;
		
	window.set_label = set_label;
	window.add_error = add_error;
	window.started_scan = started_scan;
	window.percent = 0;
	window.step_percent = 100;
	
	var SButton = {
		id: '#button_start',
		loading_id: '#button_loading',
		disabled_status:false,
		disable:function(){
			$(SButton.id).removeClass('green').addClass('gray').html('Пожалуйста подождите ...');
			$(SButton.loading_id).show();
			SButton.disabled_status = true;
		},
		enable:function(){
			$(SButton.id).removeClass('gray').addClass('green').html('Запустить процесс');
			$(SButton.loading_id).hide();
			SButton.disabled_status = false;
		},
		use: function(){
			if(SButton.disabled_status) return false;
			start_positions(<?=$audit->id;?>);
		}		
	}
	
	var load_position = function(step_id, steps_array, audit_id, keyword, region_id, region_value, steps_count, last_step, __complete){
	
		if(!steps_array[step_id]) return false;
		
		var complete = __complete || function(){};
		
		console.log('Определение позиции фразы ('+keyword.keyword+') в регионе '+region_value);
		
		window.set_label('Началось определение позиции фразы ('+keyword.keyword+') в регионе '+region_value);
		
		$.getJSON('/seo/audit/position/'+audit_id+'/?audut_keywords_id='+keyword.id+'&region_id='+region_id,{},function(position){
			if(!position.success){
				window.add_error(position.error);
			}else{
				window.set_label('Определена позиция фразы ('+keyword.keyword+') в регионе '+region_value);
				//console.log(window.percent);
			}
		})
		//.done(function(){})
		.fail(function(){
			window.add_error('Неудалось определить позицию фразы ('+keyword.keyword+') в регионе '+region_value);
		})
		.always(function(position){
			window.percent = window.percent + window.step_percent;
			$('#progress-bar').css({'width': window.percent + '%'});
			
			var memm_usage = Math.floor(position.memmory_usage/1024/1024*1000) / 1000;
			var real_memm_usage = Math.floor(position.real_memmory_usage/1024/1024*1000) / 1000;
			
			window.add_error('Использовано памяти: '+memm_usage+' Mb ('+real_memm_usage+' Mb)');
			
			if(last_step == step_id){
				window.set_label('Сбор позиций завершен!');
				Audit.positions.complete();
				$('#progress-bar').css({'width': '100%'});
				//window.location.reload();
				complete();
			}else{
				var next_id = step_id + 1;
				
				load_position(
					next_id, 
					steps_array, 
					audit_id, 
					steps_array[next_id].keyword, 
					steps_array[next_id].region_id, 
					steps_array[next_id].region_value, 
					steps_count, 
					last_step,
					complete
				);
				
			}
			//current_step++;
		});
	}
		
	var start_positions = function(audit_id){
		if(started_scan) return false;
		window.set_label('Начинаю определение. Загрузка ключевых слов...');
		
		SButton.disable();
		
		started_scan = 1;
		
		window.percent = 0;
		
		$('#progress-bar').css({'width': window.percent + '%'});
		
		$.getJSON('/seo/audit/keywords/'+audit_id,{},function(data){
			if(data.success){
				window.set_label('Ключевые слова загружены.');
				var regions = data.regions;
				var keywords = data.keywords;
				var percent = 0;
				//var current_step = 1;
				var steps = data.keywords_count * data.regions_count;
				var step_percent = 100 / steps;
				window.step_percent = step_percent;
				
				var steps_array = [];
				
				var i_step = 0;
				
				$.each(keywords,function(index,keyword){
					$.each(regions,function(region_id,region_value){
						steps_array[i_step] = {keyword:keyword,region_id:region_id,region_value:region_value};
						i_step++;
					});
				});
				
				var steps_count = steps_array.length;
				var last_step = steps_count - 1;
				
				load_position(0, steps_array, audit_id, steps_array[0].keyword, steps_array[0].region_id, steps_array[0].region_value, steps_count, last_step, function(){
					SButton.enable();
					started_scan = 0;
				});
				
			}else window.add_error(data.error);
		});
	}
	
	window.start_positions = start_positions;
</script>

<div class="tab-pane row-fluid" id="tab_4">
	<div id="progress-container">
		<label class="control-label">Определение позиций</label>
		<div class="progress">
			<div style="width: 0%;" class="bar" id="progress-bar"></div>
		</div>
		<div class='control'>
			<div class="btn-group">
				<a id="button_start" class='btn green' href='javascript:void(0);' onclick="SButton.use();return false;">Запустить процесс</a>
				<a id="button_loading" class="btn" style="background:#E5E5E5 url('/images/btn_loader.gif') center center no-repeat;display:none;">&nbsp;</a>
			</div>
		</div>
		<div id='progress-errors' style='color:red;'></div>
	</div>
</div>





</div>
</div>


</div>
</div>
</div>
</div>
</div>
</div>