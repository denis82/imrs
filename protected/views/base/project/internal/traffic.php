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
        <h5 class="panel-title text-semiold">Счетчики</h5>

                <div class="heading-elements">
                    <span class="heading-text jLastUpdate">
                    	<?= $last_update ? '<i class="icon-history position-left text-success"></i> ' . TxtHelper::DateTimeFormat( $last_update ) : '' ?>
                    </span>

                    <ul class="icons-list">
                        <li><a
                        	data-action="refresh"
                        	data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'counters')) ?>"
                        ></a></li>
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>                
    </div>
    <div class="panel-body">
        <div class="jLoadData" data-href="<?= Yii::app()->urlManager->createUrl("project/internal/load", array('id' => $model->id, 'method' => 'counters')) ?>">
            <span class="icon-spinner4 spinner"></span>
        </div>
    </div>
</div>

<script type="text/javascript">
$(function(){

    $('.panel [data-action=refresh]').click(function (e) {
        e.preventDefault();

        var $this = $(this);
        var block = $(this).parent().parent().parent().parent().parent();

        $(block).block({ 
            message: '<i class="icon-spinner2 spinner"></i>',
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                cursor: 'wait',
                'box-shadow': '0 0 0 1px #ddd'
            },
            css: {
                border: 0,
                padding: 0,
                backgroundColor: 'none'
            }
        });

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
				$(block).unblock();

				var $this = $('.panel-body', block);

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

				var $this = $('.panel-body', block);

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

});
</script>

