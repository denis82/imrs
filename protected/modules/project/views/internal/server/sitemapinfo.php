<? if ($data and $data->id): ?>

	<div class="alert alert-success alert-styled-left alert-bordered">
		Файл sitemap найден<?= $data->robots ? ' в robots.txt' : '' ?>, расположен по адресу <a href="<?= $data->url ?>" target="_blank"><?= $data->url ?></a>
	</div>

	<div style="max-height: 300px; overflow: auto;">
		<?= nl2br(htmlspecialchars($data->text)) ?>
	</div>

<? else: ?>

	<div class="alert alert-danger alert-styled-left alert-bordered">
		Файл sitemap не найден.
	</div>

<? endif; ?>