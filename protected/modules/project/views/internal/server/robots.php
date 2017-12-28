<? if ($data): ?>
	<p>
		<a href="<?= $data->url() ?>" target="_blank"><i class="icon-redo2"></i> Посмотреть на сайте</a>
	</p>

	<?= nl2br($data->text) ?>
<? else: ?>
	<div class="alert alert-info alert-styled-left alert-bordered">
		Информация недоступна в данный момент.
	</div>
<? endif; ?>