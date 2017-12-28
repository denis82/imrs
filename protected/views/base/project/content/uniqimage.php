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
        <h5 class="panel-title text-semiold">Картинки</h5>

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
    	if ($items and is_array($items)) {
    		foreach ($items as $item) {

    			$pages = $item->pagesResource();

    			?>
    			<div class="row" style="margin-bottom: 20px;">
    				<div class="col-lg-4 col-md-4">
    					<img src="<?= $item->url ?>" style="max-width: 100%; max-height: 400px;">
    				</div>
    				<div class="col-lg-8 col-md-8">
    					<div>
    						<?= htmlspecialchars($item->html) ?>
    					</div>

    					<a href="#" class="jUrl">Страницы (<?= count($pages) ?>)</a>
		                <div class="panel panel-body block-result hide">
    						<? foreach ($pages as $pres): ?>
    							<a href="<?= $pres->page->sitemap->url ?>" target="_blank"><?= $pres->page->sitemap->url ?></a><br>
    						<? endforeach; ?>
    					</div>
    				</div>
    			</div>
    			<?

    		}
    	}
    	?>

    	<?php $this->widget('CLinkPager',array('pages' => $paginator, 
    		"header" => "",
    		"firstPageLabel" => "‹‹",
    		"prevPageLabel" => "‹",
    		"lastPageLabel" => "››",
    		"nextPageLabel" => "›",
    		"selectedPageCssClass" => "active",
    		"hiddenPageCssClass" => "disabled",
    		"htmlOptions" => array('class' => "pagination"))
    	); ?>

    </div>
</div>

<script type="text/javascript">
$(function(){
    $('.jUrl').click(function(){
        $(this).parent().find('.panel').toggleClass('hide');
        return false;
    });
});
</script>
