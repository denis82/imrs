<h6 class="text-semibold">Заголовки</h6>

<? if ($data): ?>
	<?= nl2br($data->text) ?>
<? else: ?>
<div class="alert alert-info alert-styled-left alert-bordered">
	Информация недоступна в данный момент.
</div>
<? endif; ?>