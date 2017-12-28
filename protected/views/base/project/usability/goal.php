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
        <h5 class="panel-title text-semiold">Цели Яндекс.Метрики</h5>

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

    	if (count($items) == 0):

    		$this->renderPartial('//base/project/alert', array('style' => 'danger', 'text' => 'На сайте не найдено целей.'));

    	else: 

    		?>

	    	<p>На сайте найдено <?= count($items) ?> <?= Yii::t('app', 'цель|цели|целей', count($items)) ?>.</p>

	        <?

	        foreach ($items as $target) {

	            ?>

	            <div>
	                <a href="#" class="jUrl"><?= $target->value ?></a> 

	                <div class="panel panel-body block-result hide" data-href="<?= Yii::app()->urlManager->createUrl("project/usability/load", array('id' => $model->id, 'method' => 'goal', 'value' => $target->value)) ?>">
			            <span class="icon-spinner4 spinner"></span>
	                </div>
	            </div>


	            <? 

	        }

	        ?>

	        <script type="text/javascript">
	        $(function(){
	            $('.jUrl').click(function(){
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

