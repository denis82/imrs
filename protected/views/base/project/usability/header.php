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
        <h5 class="panel-title text-semiold">Анализ шапки сайта</h5>

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

    <div class="row">
    	<div class="col-md-6">
    		<h5>Главная</h5>

    		<div contenteditable="true">
		    	<? 
			    	foreach ($dom as $line) {
			    		?><div style="white-space:nowrap; display: block; overflow: hidden; text-overflow: ellipsis; padding-left: <?= $line[1] * 20 ?>px;"><?= $line[0] ?></div><?
			    	}
		    	?>
		    </div>
    	</div>
    	<div class="col-md-6">
    		<h5 style="white-space: nowrap;">Случайная (<?= $page->url ?>)</h5>

    		<div contenteditable="true">
	    		
		    	<? 
			    	foreach ($dom_rand as $line) {
			    		?><div style="white-space:nowrap; display: block; overflow: hidden; text-overflow: ellipsis; padding-left: <?= $line[1] * 20 ?>px;"><?= $line[0] ?></div><?
			    	}
		    	?>

	    	</div>
    	</div>
    </div>


    </div>
</div>

