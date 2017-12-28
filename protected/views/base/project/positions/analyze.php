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
        <h5 class="panel-title text-semiold">Анализ позиций</h5>

        <div class="heading-elements">
            <span class="heading-text jLastUpdate">
            </span>

            <ul class="icons-list">
                <li><a data-action="reload"></a></li>
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>                
    </div>

    <div class="table-responsive jSemantic">

        <table class="table table-bordered table-striped datatable-complex-header">
            <thead>
                <tr>
                    <th>№</th>
                    <th>Ключевое слово/фраза</th>
                    <th>Частотность</th>
                    <?
                        $date = new DateTime(); $date_n = 0;
                        while ($date->format('Y-m-d') >= $min_date->format('Y-m-d')) {
                            $text = $date->format('d.m.Y');

                            if ($date_n == 0) $text = 'Сегодня';
                            elseif ($date_n == 1) $text = 'Вчера';

                            ?><th><?= $text ?></th><?

                            $date->sub(  new DateInterval('P1D') );
                            $date_n++;
                        }
                    ?>
                </tr>
            </thead>

            <tbody>
                <? if ($words) {
                    foreach ($words as $n => $word) {
                        $stat  = $word->stat();

                        ?>
                        <tr id="word<?= $word->id ?>">
                            <td><?= $n+1 ?></td>
                            <td><?= $word->phrase ?></td>
                            <td class="stat <?= $stat ? '' : 'jEmpty' ?>"><?= $stat ? $stat : '<span class="icon-spinner4 spinner"></span>' ?></td>

                            <?
                                $date = new DateTime();
                                while ($date->format('Y-m-d') >= $min_date->format('Y-m-d')) {
                                    $name = $date->format('Y-m-d');

                                    ?><td><?= $positions[ $word->id ][ $name ] ? $positions[ $word->id ][ $name ] . ' <a href="' . $data[ $word->id ][ $name ]['url'] . '" title="' . $data[ $word->id ][ $name ]['title'] . '" target="_blank"><i class="icon-redo2"></i></a>' : '' ?></td><?

                                    $date->sub(  new DateInterval('P1D') );
                                }
                            ?>
                        </tr>
                        <?
                    }
                } ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    $(function(){

        $('.datatable-complex-header').DataTable({
            autoWidth: false,
            searching: false,
            paging: false,
            info: false,
            columnDefs: [{ 
                orderable: false,
                targets: [ 0 ]
            }],
            order: [[ 1, "asc" ]]
        });

        if ($('.jSemantic td.jEmpty').length) {

            var $table = $('.jSemantic');
            var data = { 'YII_CSRF_TOKEN': '<?= Yii::app()->request->csrfToken ?>' };

            $.ajax({
                type: "POST",
                url: "<?= Yii::app()->urlManager->createUrl("project/positions/load", array('id' => $model->id, 'method' => 'semantic')) ?>",
                data: data,
                success: function(html){
                    try {
                        var data = JSON.parse(html);

                        if (data.stat && data.stat !== undefined) {
                            $.each(data.stat, function(j, i){
                                 console.log(j, i);
                                $('#word' + j + ' .jEmpty').html( i ).removeClass('jEmpty');
                            });
                        }

                        if ($('.jEmpty', $table).length) {
                            $.ajax(this);
                        }
                    } catch (e) {
                        $table.before( panelBody(html) ); 
                    }
                },
                error: function(xhr){

                    $('.jEmpty', $table).html('');

                    if (xhr.responseText !== undefined) {
                        $table.before( alertDanger(xhr.responseText) );
                    }
                    else {
                        $table.before( alertDanger("Информация недоступна в данный момент.") );
                    }

                },
                dataType: 'html'
            });                             

        }

        function alertDanger( text ) {
            return panelBody('<div class="alert alert-danger alert-styled-left alert-bordered">' + text + '</div>');
        }

        function panelBody( html ) {
            return '<div class="panel-body">' + html + '</div>';
        }

    });

</script>


