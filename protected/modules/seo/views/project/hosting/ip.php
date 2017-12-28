<h6 class="text-semibold">Информация об IP</h6>

<? if ($data): ?>
	<b><?= nl2br($data->ip) ?></b><br><br>

	<? if ($whois) print nl2br($whois->text); ?>

<? else: ?>

<div class="alert alert-info alert-styled-left alert-bordered">
	Информация недоступна в данный момент.
</div>
<? endif; ?>