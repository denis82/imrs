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
        <h5 class="panel-title text-semiold">Анализ JavaScript</h5>

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

    	<p>На сайте найдено <?= count($js) ?> файлов.</p>

        <?

        foreach ($js as $url => $ids) {

            ?>

            <div>
                <a href="#" class="jUrl"><?= $url ?></a> &nbsp; <?= ($total == count($ids)) ? '' : count($ids) ?>

                <a href="<?= $url ?>" target="_blank"><i class="icon-redo2"></i></a> &nbsp; 

                <div class="panel panel-body block-result hide">
                    <?
                    	if ($total == count($ids)) {
                    		print 'На всех страницах сайта';
                    	}
                    	elseif (is_array($ids)) {

	                        foreach ($ids as $id) {
	                        	$p = Page::model()->findByPk( $id );

	                            print '<a href="'.$p->sitemap->url.'" target="_blank">' . $p->sitemap->url . '</a><br>';
	                        }
                    	}
                    ?>
                </div>
            </div>


            <? 

        }

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

