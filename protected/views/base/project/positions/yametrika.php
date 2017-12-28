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
        <h5 class="panel-title text-semiold">Яндекс.Метрика</h5>

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
    	<? if ($counters and count($counters)): ?>
	    	<div class="alert alert-success alert-styled-left alert-bordered">
	    		<? if (count($counters) == 1): ?>
	    			Доступна информация по счетчику <?= $counters[0]->id ?>
	    		<? else: ?>
	    			Доступна информация по счетчикам:
	    			<?
	    				$t = array();
	    				foreach ($counters as $c) {
	    					$t[] = $c->id;
	    				}

	    				print implode(', ', $t);
	    			?>
	    		<? endif; ?>
	    	</div>




	<? if ($stat): ?>

	<h5>Статистика за месяц</h5>

    <div class="table-responsive">
        <table class="table table-bordered table-striped datatable-complex-header">
			<thead>
				<tr>
					<th>Источник</th>
					<th>Всего</th>
					<? foreach ($stat as $j => $i): ?>
						<th><?= substr($j, 8) ?></th>
					<? endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<? foreach ($traffic_source as $j => $i): $n = 0; ?>
					<? foreach ($stat as $jj => $ii) $n += $ii[$j]; ?>
					<tr>
						<td class="text-nowrap"><?= strlen($i) ? $i : (strlen($j) ? $j : 'Источник не определен') ?></td>
						<td><b><?= $n ?></b></td>
						<? foreach ($stat as $jj => $ii): ?>
							<td><?= $ii[ $j ] ?></td>
						<? endforeach; ?>
					</tr>
				<? endforeach; ?>
			</tbody>
		</table>
	</div>

	<? endif; ?>




    	<? else: ?>
	    	<div class="alert alert-danger alert-styled-left alert-bordered">
	    		Счетчик не делегирован.
	    	</div>
    	<? endif; ?>
    </div>

</div>
