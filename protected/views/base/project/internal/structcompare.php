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

?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title text-semiold">Сравнение структуры сайта</h5>

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
                    <th>Адрес страницы</th>
                    <th>Sitemap</th>
                    <th>Перелинковка</th>
                    <th>Яндекс</th>
                </tr>
            </thead>

            <tbody>
            	<?

            	$k = 0;
            	$n = array();
            	$keys = array_keys($struct);

            	foreach ($keys as $j) {
            		$n[$j] = 0;
            	}

            	while (array_sum($n) > -3) {
            		$k++;
            		$word = '';

	            	foreach ($keys as $j) {
	            		if ($n[$j] >= 0 and count($struct[$j]) > $n[$j]) {
	            			if ($word == '') {
	            				$word = $struct[$j][ $n[$j] ];
	            			}
	            			else {
	            				$word = min($word, $struct[$j][ $n[$j] ]);
	            			}
	            		}
	            	}

            		?>
            		<tr>
            			<td>
            				<a href="<?= $word ?>" target="_blank" title="<?= $word ?>" style="display: block; overflow: hidden; width: 500px; white-space: nowrap; text-overflow: ellipsis;"><?= $word ?></div>
            			</td>
            			<?

            			foreach ($keys as $j) {
            				if ($struct[$j][ $n[$j] ] == $word) {
            					$v = '<i class="text-success icon-checkmark4"></i>';
            					$n[$j]++;
            				}
            				else {
            					$v = '<i class="text-danger icon-minus2"></i>';
            				}

            				?><td><?= $v ?></td><?
            			}

            			?>
            		</tr>
            		<?

	            	foreach ($keys as $j) {
	            		if (count($struct[$j]) <= $n[$j]) {
	            			$n[$j] = -1;
	            		}
	            	}

	            	if ($k > 10000) {
	            		break;
	            	}

            	}

            	?>
            </tbody>
        </table>
    </div>

</div>

