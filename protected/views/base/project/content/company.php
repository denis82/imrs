<?

$tpl_name = explode('/', str_replace('.php', '', __FILE__));
$tpl_name = implode('/', array_slice($tpl_name, -3) );

$tpl = TplText::model()->findByAttributes(array('name' => $tpl_name));

if (!$tpl) {
	$tpl = new TplText;
	$tpl->name = $tpl_name;
	$tpl->save();
}

if ($tpl) {
	print '<div class="panel panel-flat"><div class="panel-body jEditablePanel" data-id="'. $tpl->id .'">'. $tpl->html .'</div></div>';
}

if (Yii::app()->user->role == 'administrator') {
	print '<div class="panel panel-flat"><div class="panel-body jStaffPanel" data-name="'. $tpl_name .'">';

	foreach (TplStaff::model()->findAllByAttributes(array('name' => $tpl_name)) as $j) {
		print 
			'<div class="jStaffItem" data-id="' . $j->id . '">' . 
				$j->staff->name . ' // ' . 
				$j->staff->price . 'р * ' . $j->timer . ' = ' . ($j->staff->price * $j->timer) . 
			'</div>';
	}

	print '</div></div>';
}

?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Наличие ИНН / ОГРН</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
                    	<?= $last_update ? '<i class="icon-history position-left text-success"></i> ' . TxtHelper::DateTimeFormat( $last_update ) : '' ?>
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

        <? if (count($params['inn']) + count($params['ogrn']) > 0): ?>
            <p>
                На сайте найдены юридические реквизиты компании<br>
                <? foreach ($params['inn'] as $inn): ?>
                    ИНН: <?= $inn ?><br>
                <? endforeach; ?>

                <? foreach ($params['ogrn'] as $inn): ?>
                    ОГРН: <?= $inn ?><br>
                <? endforeach; ?>
            </p>


            <?

            foreach ($pages as $sm): 
                $p = $sm->page; 

                if (!$p) continue;

                $inn = $p->params('inn');
                $ogrn = $p->params('ogrn');

                if (count($inn) and count($ogrn)):

	                ?>

	                <div>
	                    <a href="#" class="jUrl"><?= $sm->url ?></a> &nbsp; 

	                    <b><?= count($inn) ? 'ИНН' : '' ?></b> &nbsp; 
	                    <b><?= count($ogrn) ? 'ОГРН' : '' ?></b> &nbsp; 

	                    <a href="<?= $sm->url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

	                    <div class="panel panel-body block-result hide">
	                        <?
	                            foreach ($inn as $param) {
	                                print '<b>ИНН:</b> ' . $param->value . '<br>';
	                            }
	                            foreach ($ogrn as $param) {
	                                print '<b>ОГРН:</b> ' . $param->value . '<br>';
	                            }
	                        ?>
	                    </div>
	                </div>

            	<? endif; ?>


            <? endforeach; ?>

            <script type="text/javascript">
            $(function(){
                $('.jUrl').click(function(){
                    $(this).parent().find('.panel').toggleClass('hide');
                    return false;
                });
            });
            </script>

        <? else: ?>
            <p class="alert alert-danger alert-styled-left alert-bordered">
                На сайте не найдены юридические реквизиты компании (ИНН и ОГРН)
            </p>
        <? endif; ?>

    </div>
</div>



<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Номера телефонов</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

    	<? 

    	if (count($tel) + count($phone) == 0):

    		$this->renderPartial('//base/project/alert', array('style' => 'warning', 'text' => 'На сайте не найдено номеров телефонов.'));

    	else: 

    		$tel_total = array();

    		foreach ($tel as $el) {
    			if (!in_array($el->href, $tel)) {
    				$tel_total[] = $el->href;
    			}
    		}

    		foreach ($phone as $el) {
    			if (!in_array($el->value, $tel)) {
    				$tel_total[] = $el->value;
    			}
    		}

    		?>

	    	<p>На сайте найдено <?= count($tel_total) ?> <?= Yii::t('app', 'номер телефона|номера телефона|номеров телефонов', count($tel_total)) ?>.</p>

	        <?

	        foreach ($tel_total as $el) {

	            ?>

	            <div>
	                <a href="#" class="jUrlTel"><?= $el ?></a> 

	                <div class="panel panel-body block-result hide" data-href="<?= Yii::app()->urlManager->createUrl("project/usability/load", array('id' => $model->id, 'method' => 'tel', 'tel' => $el)) ?>">
			            <span class="icon-spinner4 spinner"></span>
	                </div>
	            </div>


	            <? 

	        }

	        ?>

	        <script type="text/javascript">
	        $(function(){
	            $('.jUrlTel').click(function(){
	            	var $this = $(this).parent().find('.panel');
	                $this.toggleClass('hide');

	                if ($('.spinner', $this).length) {

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

	                }

	                return false;
	            });
	        });
	        </script>

    		<? 

    	endif; 

    	?>
    </div>
</div>



<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">E-mail адреса</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>
    <div class="panel-body">

    	<? 

    	if (count($mail) == 0):

    		$this->renderPartial('//base/project/alert', array('style' => 'warning', 'text' => 'На сайте не найдено e-mail адресов.'));

    	else: 

    		?>

	    	<p>На сайте найдено <?= count($mail) ?> e-mail <?= Yii::t('app', 'адрес|адреса|адресов', count($mail)) ?>.</p>

	        <?

	        foreach ($mail as $link) {

	            ?>

	            <div>
	                <a href="#" class="jUrlMail"><?= $link ?></a> 

	                <div class="panel panel-body block-result hide" data-href="<?= Yii::app()->urlManager->createUrl("project/usability/load", array('id' => $model->id, 'method' => 'mail', 'link' => $link)) ?>">
			            <span class="icon-spinner4 spinner"></span>
	                </div>
	            </div>


	            <? 

	        }

	        ?>

	        <script type="text/javascript">
	        $(function(){
	            $('.jUrlMail').click(function(){
	            	var $this = $(this).parent().find('.panel');
	                $this.toggleClass('hide');

	                if ($('.spinner', $this).length) {

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

	                }

	                return false;
	            });
	        });
	        </script>

    		<? 

    	endif; 

    	?>
    </div>
</div>
