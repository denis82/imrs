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
        <h5 class="panel-title text-semiold">Наличие запрещенного контента</h5>

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

    	<? if (!$pages or count($pages) == 0): ?>
			<div class="alert alert-success alert-styled-left alert-bordered">
				Запрещенный контент отсутствует.
			</div>
    	<? else: ?>
			<div class="alert alert-danger alert-styled-left alert-bordered">
				На сайте найдены страницы со стоп-словами.
			</div>

			<?

			foreach ($pages as $p) {
				$sm = $p->sitemap;
	            ?>
	            <div>
	                <?= $sm->url ?> &nbsp; 
	                <a href="<?= $sm->url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; <br>
	                <span class="text-semibold">Найденные слова:</span> <?= implode(', ', $p->stopWords()) ?>
	            </div>
	            <? 
			}

			?>
    	<? endif; ?>
    </div>
</div>

