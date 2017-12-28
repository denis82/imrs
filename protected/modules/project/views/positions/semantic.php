<? if ($report): ?>

	<?= $report->report_id ?>
	<?= $report->status ?>

<? else: ?>

	<p class="alert alert-danger alert-styled-left alert-bordered">Данные не найдены.</p>

<? endif; ?>
