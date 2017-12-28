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
        <h5 class="panel-title text-semiold">Формы на сайте</h5>

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

    	<p>На сайте найдено <?= count($forms) ?> форм.</p>

        <?

        /*foreach ($forms as $url => $ids) {*/
        foreach ($forms as $r) {

            ?>

            <div>
                <a href="#" class="jUrl"><?= $r->hash ?></a> 
                <?/*&nbsp; <?= ($total == count($ids)) ? '' : count($ids) ?>
                <a href="<?= $url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; */?>

                <div class="panel panel-body block-result hide" data-href="<?= Yii::app()->urlManager->createUrl("project/usability/load", array('id' => $model->id, 'method' => 'form', 'form_id' => $r->id)) ?>">
		            <span class="icon-spinner4 spinner"></span>
                </div>

            </div>

            <div class="row">

            	<div class="col-lg-6 col-md-6" contenteditable="true" style="max-height: 500px; overflow: auto;">
            		<?= htmlspecialchars( nl2br($r->html) ) ?>
            	</div>

            	<div class="col-lg-6 col-md-6">
            		<?= $r->html ?>
            	</div>


                <?/*div class="panel panel-body block-result hide">
                    <?
                    	if ($total == count($ids)) {
                    		print 'На всех страницах сайта';
                    	}
                    	elseif (is_array($ids)) {

	                        foreach ($ids as $id) {
	                        	$p = Page::model()->findByPk( $id );

	                            print '<a href="'.$p->sitemap->url.'" target="_blank">' . $p->sitemap->url . '</a><br>';
	                        }
                    	}
                    ?>
                </div*/?>
            </div>

            <br>


            <? 

        }

/*        foreach ($res as $url => $ids) {

            ?>

            <div>
                <a href="#" class="jUrl"><?= $url ?></a> &nbsp; <?= ($total == count($ids)) ? '' : count($ids) ?>

                <a href="<?= $url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

                <div class="panel panel-body block-result hide">
                    <?
                    	if ($total == count($ids)) {
                    		print 'На всех страницах сайта';
                    	}
                    	elseif (is_array($ids)) {

	                        foreach ($ids as $id) {
	                        	$p = Page::model()->findByPk( $id );

	                            print '<a href="'.$p->sitemap->url.'" target="_blank">' . $p->sitemap->url . '</a><br>';
	                        }
                    	}
                    ?>
                </div>
            </div>


            <? 

        }*/

        ?>

        <?/*script type="text/javascript">
        $(function(){
            $('.jUrl').click(function(){
                $(this).parent().find('.panel').toggleClass('hide');
                return false;
            });
        });
        </script*/?>

    </div>
</div>



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
