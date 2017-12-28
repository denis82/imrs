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
        <h5 class="panel-title text-semiold">Мета-тег viewport</h5>

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
        <? if ($viewport): ?>
            <div class="alert alert-success alert-styled-left alert-bordered">
                <span class="text-semibold">Viewport</span> найден.<br>

                <? foreach ($viewport->textValues() as $j => $i): ?>
                    <span class="text-semibold"><?= $i['title'] ? $i['title'] : $i['name'] ?></span> =
                    <?= $i['text'] ? $i['text'] : $i['value'] ?><br>
                <? endforeach; ?>
            </div>
        <? else: ?>
            <div class="alert alert-danger alert-styled-left alert-bordered">
                <span class="text-semibold">Viewport</span> не найден.
            </div>
        <? endif; ?>
    </div>
</div>

