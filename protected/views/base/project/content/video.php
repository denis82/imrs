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
        <h5 class="panel-title text-semiold">Наличие видео</h5>

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

        <?

        foreach ($pages as $sm): 
            $p = $sm->page; 

            if (!$p) continue;

            $video = $p->params('video');

            if (count($video) == 0) continue;

            ?>

            <div>
                <a href="#" class="jUrl"><?= $sm->url ?></a> &nbsp; 

                <a href="<?= $sm->url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

                <div class="panel panel-body block-result hide">
                    <?
                        foreach ($video as $param) {
                            print '<a href="' . $param->value . '" target="_blank">' . $param->value . '</a><br>';
                        }
                    ?>
                </div>
            </div>


            <? 
        endforeach; 

        ?>

        <script type="text/javascript">
        $(function(){
            $('.jUrl').click(function(){
                $(this).parent().find('.panel').toggleClass('hide');
                return false;
            });
        });
        </script>

    </div>
</div>

