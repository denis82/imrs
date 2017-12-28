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
        <h5 class="panel-title text-semiold">Структура и вес страниц</h5>

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

    <div class="table-responsive jSemantic">

        <table class="table table-bordered table-striped datatable-complex-header">
            <thead>
                <tr>
                    <th>URL</th>
                    <th>Вх</th>
                    <th>Исх</th>
                    <th>PR1</th>
                    <th>PR2</th>
                    <th>PR3</th>
                </tr>
            </thead>

            <tbody>

                <? if ($pages) {
                    foreach ($pages as $page) {
                        ?>
                        <tr>
                            <td>
                            	<div style="width: 400px; overflow: hidden; " title="<?= $page['url'] ?>"><?= $page['url'] ?></div>
                            </td>
                            <td><?= $page['in'] ?></td>
                            <td><?= $page['out'] ?></td>
                            <td><?= number_format($page['rank1'], 1) ?></td>
                            <td><?= number_format($page['rank2'], 2) ?></td>
                            <td><?= number_format($page['rank3'], 3) ?></td>
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
            order: [[ 3, "desc" ]]
        });

    });

</script>


