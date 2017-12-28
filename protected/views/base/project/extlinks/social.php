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
        <h5 class="panel-title text-semiold">Ссылки на социальные сети</h5>

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

		<div class="table-responsive">
			<table class="table">
				<tbody>
					<? foreach (array(
						'vk.com' => 'Вконтакте',
						'facebook.com' => 'Facebook',
						'twitter.com' => 'Twitter',
						'google.com' => 'Google+',
						'ok.ru' => 'Одноклассники',
						'linkedin.com' => 'LinkedIn',
						'instagram.com' => 'Instagram',
						'livejournal.com' => 'LiveJournal',
					) as $key => $title): ?>

					<tr>
						<td><?= $title ?></td>
						<td>
							<?
    							if ($links[ $key ] and is_array($links[ $key ])) {
    								foreach ($links[ $key ] as $link) {
    									print '<a href="'.$link.'" target="_blank">' . urldecode( $link ) . '</a><br>';
    								}
    							}
							?>
						</td>
					</tr>

					<? endforeach; ?>
				</tbody>
			</table>
		</div>    	
    </div>
</div>

