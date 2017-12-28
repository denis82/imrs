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

<?

if (count($fonts)):

foreach ($fonts as $name => $font):
    ?>

    <div class="panel panel-flat">
        <div class="panel-heading">
            <h5 class="panel-title">Шрифт <span class="text-semibold"><?= $font->family ?></span></h5>
        </div>
        <div class="panel-body">
        	<?
        	if ($fonts_source[$name] and is_array($fonts_source[$name])) {
        		?><p><?

	        	foreach ($fonts_source[$name] as $j => $i) {

	        		?><a href="<?= $j ?>" target="_blank"><?= $j ?></a><br><?

	        	}

	        	?></p><?
        	}

        	?>

        	<style type="text/css">
        	<?

	            foreach ($font->face as $face):
	            	print $face->style('customFont') . "\n";
	            endforeach;

        	?>
        	</style>
            <?

            $used = array();

            foreach ($font->face as $face):
            	$hash = $face->getClassHash();

            	if (in_array($hash, $used)) continue;

            	$used[] = $hash;

                ?>
                <style>
                    <?= $face->exampleStyle('customFont') ?>
                </style>

                <h6>Стиль <span class="text-semibold"><?= $face->style ?></span>, толщина <span class="text-semibold"><?= $face->weight ?></span></h6>

                <p class="text-size-xx-large mb-20 <?= $face->className('customFont') ?>">
                    <?= $font->panagram('ru') ?><br>
                    <?= $font->panagram('en') ?>
                </p>

                <?
            endforeach;

            ?>
        </div>
    </div>

    <?

endforeach;

else:

    ?>
    <div class="alert alert-success alert-styled-left alert-bordered">Сторонних шрифтов не найдено.</div>
    <?

endif;
