<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Панель управления | <?= $this->title; ?></title>
</head>

<body>

	<!-- Main navbar -->
	<div class="navbar navbar-default header-highlight">
		<div class="navbar-header">
			<a class="navbar-brand" href="<?= Yii::app()->urlManager->createUrl('site/index'); ?>" title="<?= Yii::app()->name; ?>"><img src="/html/assets/images/logo_light.png" alt=""></a>

			<ul class="nav navbar-nav visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown language-switch">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="/html/assets/images/flags/gb.png" class="position-left" alt="">
						English
						<span class="caret"></span>
					</a>

					<ul class="dropdown-menu">
						<li><a class="deutsch"><img src="/html/assets/images/flags/de.png" alt=""> Deutsch</a></li>
						<li><a class="ukrainian"><img src="/html/assets/images/flags/ua.png" alt=""> Українська</a></li>
						<li><a class="english"><img src="/html/assets/images/flags/gb.png" alt=""> English</a></li>
						<li><a class="espana"><img src="/html/assets/images/flags/es.png" alt=""> España</a></li>
						<li><a class="russian"><img src="/html/assets/images/flags/ru.png" alt=""> Русский</a></li>
					</ul>
				</li>

				<? $user = Yii::app()->user->getModel(); ?>
				<? if ($user and $user->id): ?>

					<li class="dropdown dropdown-user">

						<a class="dropdown-toggle" data-toggle="dropdown">
							<img src="<?= $user->photo ? $user->getSmallAvatar() : '/html/assets/images/placeholder.jpg'; ?>">
							<span><?= (isset($user)? $user->name : 'User'); ?></span>
							<i class="caret"></i>
						</a>

						<ul class="dropdown-menu dropdown-menu-right">
							<li><a href="<?= Yii::app()->urlManager->createUrl('main/user/profile'); ?>"><i class="icon-user-plus"></i> Мой профиль</a></li>
							<li class="divider"></li>
							<li><a href="<?= Yii::app()->urlManager->createUrl('site/logout'); ?>"><i class="icon-switch2"></i> Выйти</a></li>
						</ul>
					</li>

				<? endif; ?>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->


	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
		<div class="page-content">

			<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="<?= Yii::app()->urlManager->createUrl("seo/project/new"); ?>" class="media-left media-create media-create-icon">
									<i class="icon-plus-circle2"></i>
								</a>
								<div class="media-body">
									<a href="<?= Yii::app()->urlManager->createUrl("seo/project/new"); ?>" class="media-left media-create">
										<span class="media-heading text-semibold">Добавить сайт</span>
									</a>
								</div>
							</div>
						</div>
					</div>

					<? 

					if (Yii::app()->user->id):

						if ($this->project) {
							echo $this->renderPartial('//menu/project', array('model' => $this->project));
							echo $this->renderPartial('//menu/data', array(
								'model' => $this->project, 
								'items' => Yii::app()->controller->module->getMenu( $this->project )
							));
						}

						foreach (Project::model()->findAllByAttributes(array('user_id' => Yii::app()->user->id)) as $p) {
							if (!$this->project or $this->project->id != $p->id) {
								echo $this->renderPartial('//menu/project', array('model' => $p));
							}
						}

					endif;
					
					?>

				</div>
			</div>
			<!-- /main sidebar -->


			<!-- Main content -->
			<div class="content-wrapper">

				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><span class="text-semibold"><?= $this->title ?></span> <?= $this->description ? '- ' . $this->description : '' ?></h4>
						</div>
					</div>

					<div class="breadcrumb-line breadcrumb-line-component">
						<ul class="breadcrumb">
							<? if (($this->id == 'site' && $this->action->id == 'index')): ?>
								<li><i class="icon-home2"></i> Рабочий стол</li>
							<? else: ?>
								<li><a href="<?= Yii::app()->urlManager->createUrl("site/index"); ?>"><i class="icon-home2"></i> Рабочий стол</a> </li>
							<? endif; ?>
							<? foreach ($this->breadcrumbs as $key => $crumb): ?>
								<? if (is_string($key)): ?>
									<li><a href="<?= $crumb ?>"><?= $key ?></a></li>
								<? else: ?>
									<li class="active"><span><?= $crumb ?></span></li>
								<? endif; ?>
							<? endforeach; ?>								
						</ul>
					</div>
				</div>
				<!-- /page header -->

				<!-- Content area -->
				<div class="content">

					<?= $content ?>

					<script type="text/javascript">
					    $(function(){

					        $('.jLoadData').each(function(){
					            var $this = $(this);
					            var href = $this.data('href');

					            if (!href || href === undefined) {
					            	$this.html('<div class="alert alert-danger alert-styled-left alert-bordered">Невозможно загрузить данные.</div>');
					            	return ;
					            }

					            var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

								$.ajax({
									type: "POST",
									url: href,
									data: data,
									success: function(html){
										try {
										    var data = JSON.parse(html);

										    if (data.retry !== undefined) {

										    	var this_params = this;

										    	setTimeout(
										    		function(){ $.ajax(this_params); }, 
										    		data.retry * 1000
										    	);

											    if (data.html !== undefined) {
												    $this.html( data.html );
											    }
											    
										    }
										    else {

											    if (data.last_update !== undefined) {
											    	var $el = $this.closest('.panel').find('.jLastUpdate');
											    	$el.html( '<i class="icon-history position-left text-success"></i> ' + data.last_update );
											    }

											    if (data.html !== undefined) {
												    $this.html( data.html );
											    }
										    }

										} catch (e) {
											$this.html( html ); 
										}
									},
									error: function(xhr){

										if (xhr.responseText !== undefined) {
											$this.html('');

											$('<div class="alert alert-danger alert-styled-left alert-bordered" />')
												.html( xhr.responseText )
												.appendTo( $($this) );
										}
										else {
										    $this.html( '<div class="alert alert-danger alert-styled-left alert-bordered">Информация недоступна в данный момент.</div>' );
										}
									},
									dataType: 'html'
								});					            
					        });

							<? if (Yii::app()->user->id == 11 or Yii::app()->user->id == 6): ?>
								$('.jEditablePanel').each(function(){
									$(this).prepend('<div class="jEditablePanelEdit"><a href="#">Изменить</a></div>');
								});
							<? endif; ?>

							$('.jEditablePanel')
								.delegate('.jEditablePanelEdit a', 'click', function(){
									var $this = $(this).closest('.panel-body');

									$(this).parent().remove();

									var html = $this.html();

									$this.html('<div class="form-group"><textarea class="form-control" rows="10"></textarea></div>');
									$('textarea', $this).val( html ); 

									$this.append('<div class="pull-right"><button type="button" class="btn btn-success jEditablePanelSave"><i class="icon-checkmark4"></i> Сохранить</button></div>');
									$this.append('<div class="text-left"><button type="button" class="btn btn-danger jEditablePanelCancel"><i class="icon-arrow-left8"></i> Отмена</button></div>');

									$('button.jEditablePanelCancel', $this).click(function(){
										$this.html( '<div class="jEditablePanelEdit"><a href="#">Изменить</a></div>' + html );
									});

									return false;
								})

								.delegate('.jEditablePanelSave', 'click', function(){
									var $this = $(this).closest('.panel-body');

									var html = $('textarea', $this).val();

									$.post(
										'/project/index/tpltext',
										{
											'id' : $this.data('id'),
											'html' : html,
											'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>'
										},
										function(r){
											console.log(r);
										}
									);

									$this.html( '<div class="jEditablePanelEdit"><a href="#">Изменить</a></div>' + html );

									return false;
								})
							;

							$('.jStaffPanel')
								.each(function(){
									var $this = $(this);

									var params = [];

									$.post(
										'/admin/index/tplstaff',
										{
											'id' : $this.data('name'),
											'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>'
										},
										function(r){
											$this.html('<div class="jStaffPanelEdit"><a href="#">Изменить</a></div>');

											var staff = [];

											for (var j = 0; j < r.staff.length; j++) {
												staff[ r.staff[j].id ] = r.staff[j]
											}

											for (var j = 0; j < r.tpl.length; j++) {
												var $sel = $('<div class="jStaffItem" />');

												$sel.append( staff[r.tpl[j].staff_id].name );

												if (r.tpl[j].text && r.tpl[j].text.length > 0) {
													$sel.append( ' (' + r.tpl[j].text + ')' );
												}

												$sel.append( '. Стоимость, за час: ' + staff[r.tpl[j].staff_id].price + ' руб. * ' );
												$sel.append( r.tpl[j].timer + ' час. = ' );
												$sel.append( staff[r.tpl[j].staff_id].price * r.tpl[j].timer );
												$sel.append( 'рублей. ');
												if (r.tpl[j].multiple == 1) {
													$sel.append( 'Цена за исправление одной ошибки. ');
												}
												$sel.append( 'Работа выполняется ' + r.tpl[j].period_txt + '.' );


												$sel.appendTo( $($this) );
											}

										},
										'json'
									);

								})
								.delegate('.jStaffPanelEdit a', 'click', function(){
									var $this = $(this).closest('.jStaffPanel');

									$this.html('<div class="form-layer"></div>');

									$this.append(
										'<div>' +
											'<div class="pull-right"><button type="button" class="btn btn-success jStaffPanelSave"><i class="icon-checkmark4"></i> Сохранить</button></div>' +
											'<div class="text-left"><button type="button" class="btn btn-danger jStaffPanelCancel"><i class="icon-arrow-left8"></i> Отмена</button></div>' +
										'</div>'
									);

									$.post(
										'/admin/index/tplstaff',
										{
											'id' : $this.data('name'),
											'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>'
										},
										function(r){
											var $line = $('<div class="form-group row" />');
											$line.append('<input type="hidden" name="tpl_id[]" value="0">');
											$line.append('<div class="col-lg-2 col-md-2"><select name="staff_id[]" class="jStaff form-control" /></div>');
											for (var j = 0; j < r.staff.length; j++) {
												$('select', $line).append('<option value="' + r.staff[j].id + '">' + r.staff[j].name + '</option>');
											}
											$line.append('<div class="col-lg-5 col-md-4"><input type="text" name="text[]" class="form-control jText" placeholder="что будет сделано" /></div>');
											$line.append('<div class="col-lg-1 col-md-1"><input type="text" name="timer[]" class="form-control jTimer" /></div>');
											$line.append('<div class="col-lg-1 col-md-1" style="line-height: 36px;">час</div>');
											$line.append('<div class="col-lg-1 col-md-1" style="line-height: 36px;"><input type="checkbox" name="multiple[]" value="1" class="jMultiple" /></div>');
											$line.append('<div class="col-lg-1 col-md-2"><select name="staff_period[]" class="form-control jPeriod" /></div>');
												$('select.jPeriod', $line).append('<option value="once">разово</option>');
												$('select.jPeriod', $line).append('<option value="day">ежедневно</option>');
												$('select.jPeriod', $line).append('<option value="week">еженедельно</option>');
												$('select.jPeriod', $line).append('<option value="month">ежемесячно</option>');
												$('select.jPeriod', $line).append('<option value="quart">ежеквартально</option>');
												$('select.jPeriod', $line).append('<option value="year">ежегодно</option>');
											$line.append('<div class="col-lg-1 col-md-1"><i class="icon-plus-circle2 jStaffDuplicate" style="cursor: pointer; display: inline-block; line-height: 36px;"></i></div>');

											if (r.tpl !== undefined && r.tpl) {
												for (var j = 0; j < r.tpl.length; j++) {
													var $sel = $line.clone();

													$('input[type=hidden]', $sel).val( r.tpl[j].id );
													$('select.jStaff', $sel).val( r.tpl[j].staff_id );
													$('input.jText', $sel).val( r.tpl[j].text );
													$('input.jTimer', $sel).val( r.tpl[j].timer );
													$('select.jPeriod', $sel).val( r.tpl[j].period );
													$('i', $sel).replaceWith('<i class="icon-cancel-circle2 jStaffRemove" style="cursor: pointer; display: inline-block; line-height: 36px;"></i>');

													if (r.tpl[j].multiple == 1) {
														$('input.jMultiple', $sel).attr('checked', 'checked');
													}

													$('select.jPeriod', $sel).val( r.tpl[j].period );

													$sel.appendTo( $('.form-layer', $this) );
												}
											}

											$line.appendTo( $('.form-layer', $this) );
										},
										'json'
									);

									return false;
								})
								.delegate('.jStaffDuplicate', 'click', function(){
									var $this = $(this).closest('.jStaffPanel');
									var $el = $(this).closest('.form-group').clone();
									$('input', $el).val('');
									$el.appendTo( $('.form-layer', $this) );
									$(this).replaceWith('<i class="icon-cancel-circle2 jStaffRemove" style="cursor: pointer; display: inline-block; line-height: 36px;"></i>');

									return false;
								})
								.delegate('.jStaffRemove', 'click', function(){
									$(this).closest('.form-group').remove();
									return false;
								})
								.delegate('.jStaffPanelSave', 'click', function(){
									var $this = $(this).closest('.jStaffPanel');

									var params = [];

									$('.form-group', $this).each(function(){
										var el = {};

										el.id = $('input[type=hidden]', this).val();
										el.staff = $('select.jStaff', this).val();
										el.text = $('input.jText', this).val();
										el.timer = $('input.jTimer', this).val();
										el.period = $('select.jPeriod', this).val();

										if ($('input.jMultiple', this).is(':checked')) {
											el.multiple = 1;
										}
										else {
											el.multiple = 0;
										}

										params.push( el );
									});

									$.post(
										'/admin/index/tplstaffsave',
										{
											'id' : $this.data('name'),
											'params' : params,
											'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>'
										},
										function(r){
											$this.html('<div class="jStaffPanelEdit"><a href="#">Изменить</a></div>');

											var staff = [];

											for (var j = 0; j < r.staff.length; j++) {
												staff[ r.staff[j].id ] = r.staff[j]
											}

											for (var j = 0; j < r.tpl.length; j++) {
												var $sel = $('<div class="jStaffItem" />');

												$sel.append( staff[r.tpl[j].staff_id].name );

												if (r.tpl[j].text && r.tpl[j].text.length > 0) {
													$sel.append( ' (' + r.tpl[j].text + ')' );
												}

												$sel.append( '. Стоимость, за час: ' + staff[r.tpl[j].staff_id].price + ' руб. * ' );
												$sel.append( r.tpl[j].timer + ' час. = ' );
												$sel.append( staff[r.tpl[j].staff_id].price * r.tpl[j].timer );
												$sel.append( 'рублей. ');
												if (r.tpl[j].multiple == 1) {
													$sel.append( 'Цена за исправление одной ошибки. ');
												}
												$sel.append( 'Работа выполняется ' + r.tpl[j].period_txt + '.' );

												$sel.appendTo( $($this) );
											}

										},
										'json'
									);

									return false;
								})
							;

					    });
					</script>					

					<!-- Footer -->
					<div class="footer text-muted">
						&copy; <?= date('Y') ?> СЕО Эксперт
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->

</body>
</html>




<?/*

						<ul class="nav">
							<? foreach ($this->menu as $menuItem): ?>
								<? foreach ($menuItem['items'] as $menuSubItem): ?>
									<li class="<?= $menuSubItem['active'] ? 'active' : '' ?>">										
										<a href="<?= $menuSubItem['url'] ?>">
											<!--<i class="icon-<?= $menuSubItem['icon'] ?>"></i>--> 
											<?= $menuSubItem['label'] ?>
										</a>
									</li>
								<? endforeach; ?>		 
							<? endforeach; ?> 
						</ul>



						<h3 class="page-title">
							<?= $this->title ?>					
							<!--<small><?= $this->description ?></small>-->
						</h3>




		



*/?>