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
        <h5 class="panel-title text-semiold">Слова</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>

    <div class="panel-pages jPages">

        <!--div class="pages-item">
            <a href="#" data-id="0">По всему сайту</a>
        </div-->

        <? foreach ($pages as $page): ?>
            <div class="pages-item">
                <a href="#" data-id="<?= $page->id ?>"><?= $page->url ?></a>
            </div>
        <? endforeach; ?>

    </div>
</div>

<script type="text/javascript">
    $(function(){
        $('.jPages a').click(function(){
            var $this = $(this);
            var $div = $this.closest('.jPages');
            var $btn = $(this).parent();

            $('.jPagesResult', $div).remove();

            var $el = $('<div />')
                .addClass('jPagesResult')
                .html('<span class="icon-spinner4 spinner"></span>')
                .insertAfter( $($btn) )
            ;

            var data = { 
                'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>',
                'page': $this.data('id')
            };

            $.ajax({
                type: "POST",
                url: "<?= Yii::app()->urlManager->createUrl("project/positions/load", array('id' => $model->id, 'method' => 'words')) ?>",
                data: data,
                success: function(html){
                    try {
                        var data = JSON.parse(html);
                        $el.html( data.content ); 
                    } catch (e) {
                        $el.html( html ); 
                    }
                },
                error: function(xhr){

                    $('.jEmpty', $table).html('');

                    if (xhr.responseText !== undefined) {
                        $el.html( alertDanger(xhr.responseText) );
                    }
                    else {
                        $el.html( alertDanger("Информация недоступна в данный момент.") );
                    }

                },
                dataType: 'html'
            });                             

            return false;
        });

        $('.jPages').delegate('a.add2semantic', 'click', function(){
            var $el = $(this).closest('td');

            phrase = $(this).data('text');

            var data = { 
                'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>',
                'phrase': phrase
            };

            $.ajax({
                type: "POST",
                url: "<?= Yii::app()->urlManager->createUrl("project/positions/load", array('id' => $model->id, 'method' => 'addSemantic')) ?>",
                data: data,
                success: function(html){
                    try {
                        var data = JSON.parse(html);
                        $el.html( data.content ); 
                    } catch (e) {
                        $el.html( html ); 
                    }
                },
                error: function(xhr){

                    $('.jEmpty', $table).html('');

                    if (xhr.responseText !== undefined) {
                        $el.html( alertDanger(xhr.responseText) );
                    }
                    else {
                        $el.html( alertDanger("Информация недоступна в данный момент.") );
                    }

                },
                dataType: 'html'
            });

            return false;
       });

        function alertDanger( text ) {
            return '<div class="alert alert-danger alert-styled-left alert-bordered">' + text + '</div>';
        }

    });
</script>

