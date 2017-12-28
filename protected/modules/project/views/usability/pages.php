<? $this->renderPartial('//base/project/alert', array(
	'style' => 'info', 
	'text' => (count($pages) >= 100) ? 
		'Найдено более чем на 100 страницах сайта.' : 
		'Найдено на ' . count($pages) . ' ' . 
			Yii::t('app', 'странице сайта|страницах сайта|страницах сайта', count($pages)) 
)); ?>

<? foreach ($pages as $page): $href = $page->sitemap->url; ?>
<div>
	<a href="<?= $href ?>" target="_blank"><?= $href ?></a>
</div>
<? endforeach; ?>